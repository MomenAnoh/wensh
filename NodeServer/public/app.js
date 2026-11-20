const form = document.querySelector('#room-form');
const userIdInput = document.querySelector('#user-id');
const connectButton = document.querySelector('#connect-button');
const connectionStatus = document.querySelector('#connection-status');
const connectionLabel = document.querySelector('#connection-label');
const roomLabel = document.querySelector('#room-label');
const offersList = document.querySelector('#offers-list');
const offerCount = document.querySelector('#offer-count');
const emptyState = document.querySelector('#empty-state');

let socket = null;
let receivedOffers = 0;

function setConnectionState(state, label) {
    connectionStatus.dataset.state = state;
    connectionLabel.textContent = label;
}

function formatPrice(value) {
    const amount = Number(value);

    if (!Number.isFinite(amount)) {
        return 'السعر غير متاح';
    }

    return new Intl.NumberFormat('ar-EG', {
        style: 'currency',
        currency: 'EGP',
        maximumFractionDigits: 2,
    }).format(amount);
}

function addOffer(data) {
    emptyState.hidden = true;
    receivedOffers += 1;
    offerCount.textContent = String(receivedOffers);

    const card = document.createElement('article');
    card.className = 'offer-card';

    const main = document.createElement('div');
    main.className = 'offer-main';

    const title = document.createElement('h3');
    title.className = 'offer-title';
    title.textContent = String(data?.offer ?? 'عرض جديد');

    const meta = document.createElement('p');
    meta.className = 'offer-meta';
    meta.textContent = `وصل في ${new Intl.DateTimeFormat('ar-EG', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
    }).format(new Date())}`;

    const price = document.createElement('strong');
    price.className = 'offer-price';
    price.textContent = formatPrice(data?.final_price ?? data?.total_price ?? data?.price);

    main.append(title, meta);
    card.append(main, price);
    offersList.prepend(card);
}

function connectToUserRoom(userId) {
    if (socket) {
        socket.disconnect();
    }

    receivedOffers = 0;
    offerCount.textContent = '0';
    offersList.replaceChildren(emptyState);
    emptyState.hidden = false;

    const room = `user_${userId}`;
    roomLabel.textContent = `يستمع الآن إلى الغرفة ${room}`;
    setConnectionState('connecting', 'جارٍ الاتصال');
    connectButton.disabled = true;

    socket = io({ auth: { room } });

    socket.on('connect', () => {
        setConnectionState('online', 'متصل');
        connectButton.disabled = false;
    });

    socket.on('connect_error', () => {
        setConnectionState('offline', 'تعذر الاتصال');
        connectButton.disabled = false;
    });

    socket.on('disconnect', () => {
        setConnectionState('offline', 'غير متصل');
        connectButton.disabled = false;
    });

    socket.on('receive_offer', addOffer);
}

form.addEventListener('submit', (event) => {
    event.preventDefault();

    const userId = userIdInput.value.trim();

    if (userId) {
        const url = new URL(window.location.href);
        url.searchParams.set('user_id', userId);
        window.history.replaceState({}, '', url);
        connectToUserRoom(userId);
    }
});

const userIdFromUrl = new URLSearchParams(window.location.search).get('user_id');

if (userIdFromUrl) {
    userIdInput.value = userIdFromUrl;
    connectToUserRoom(userIdFromUrl);
}
