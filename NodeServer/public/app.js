const form = document.querySelector('#room-form');
const userIdInput = document.querySelector('#user-id');
const roomTypeInput = document.querySelector('#room-type');
const roomIdLabel = document.querySelector('#room-id-label');
const connectButton = document.querySelector('#connect-button');
const connectionStatus = document.querySelector('#connection-status');
const connectionLabel = document.querySelector('#connection-label');
const roomLabel = document.querySelector('#room-label');
const offersList = document.querySelector('#offers-list');
const offerCount = document.querySelector('#offer-count');
const emptyState = document.querySelector('#empty-state');
const acceptedOfferModal = document.querySelector('#offer-accepted-modal');
const paymentModalOrder = document.querySelector('#payment-modal-order');
const paymentModalPrice = document.querySelector('#payment-modal-price');
const paymentModalFees = document.querySelector('#payment-modal-fees');
const paymentModalTotal = document.querySelector('#payment-modal-total');

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

function addOffer(payload, eventName = 'receive_offer') {
    const offer = payload?.offer_data ?? payload;
    const accepted = eventName === 'offer_accepted';

    emptyState.hidden = true;
    receivedOffers += 1;
    offerCount.textContent = String(receivedOffers);

    const card = document.createElement('article');
    card.className = 'offer-card';

    const main = document.createElement('div');
    main.className = 'offer-main';

    const title = document.createElement('h3');
    title.className = 'offer-title';
    title.textContent = accepted
        ? offer?.id == null ? 'تم قبول العرض' : `تم قبول العرض رقم ${offer.id}`
        : offer?.id == null ? 'عرض جديد' : `عرض جديد رقم ${offer.id}`;

    const meta = document.createElement('p');
    meta.className = 'offer-meta';
    meta.textContent = `وصل في ${new Intl.DateTimeFormat('ar-EG', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
    }).format(new Date())}`;

    const details = document.createElement('dl');
    details.className = 'offer-details';

    const statusLabels = {
        pending: 'قيد الانتظار',
        accepted: 'مقبول',
        rejected: 'مرفوض',
    };
    const sourceLabels = {
        user: 'مستخدم',
        wensh: 'ونش',
    };
    const fields = [
        ['رقم الطلب', offer?.order_id],
        ['الحالة', statusLabels[offer?.status] ?? offer?.status],
        ['مصدر العرض', sourceLabels[offer?.made_from] ?? offer?.made_from],
        ['السعر', offer?.price == null ? null : formatPrice(offer.price)],
        ['الرسوم', offer?.fees == null ? null : formatPrice(offer.fees)],
        ['معرف المستخدم', offer?.user_id],
        ['معرف الونش', offer?.wensh_id],
        ['تاريخ الإنشاء', offer?.created_at],
        ['آخر تحديث', offer?.updated_at],
    ];

    fields.forEach(([label, value]) => {
        const item = document.createElement('div');
        item.className = 'offer-detail';

        const term = document.createElement('dt');
        term.textContent = label;

        const description = document.createElement('dd');
        description.textContent = value == null || value === '' ? '—' : String(value);

        item.append(term, description);
        details.append(item);
    });

    const price = document.createElement('strong');
    price.className = 'offer-price';
    price.textContent = formatPrice(
        offer?.total_price ?? payload?.final_price ?? payload?.total_price ?? payload?.price,
    );

    main.append(title, meta, details);
    card.append(main, price);
    offersList.prepend(card);
}

function showAcceptedOffer(payload) {
    const offer = payload?.offer_data ?? payload;

    paymentModalOrder.textContent = offer?.order_id == null
        ? 'تم تأكيد قبول العرض.'
        : `تم قبول العرض على الطلب رقم ${offer.order_id}.`;
    paymentModalPrice.textContent = formatPrice(offer?.price);
    paymentModalFees.textContent = formatPrice(offer?.fees);
    paymentModalTotal.textContent = formatPrice(
        offer?.total_price ?? payload?.final_price ?? payload?.total_price,
    );

    if (!acceptedOfferModal.open) {
        acceptedOfferModal.showModal();
    }
}

function closeAcceptedOfferModal() {
    acceptedOfferModal.close();
}

function getRoom(accountType, accountId) {
    return `${accountType}_${accountId}`;
}

function connectToRoom(accountType, accountId) {
    if (socket) {
        socket.disconnect();
    }

    receivedOffers = 0;
    offerCount.textContent = '0';
    offersList.replaceChildren(emptyState);
    emptyState.hidden = false;

    const room = getRoom(accountType, accountId);
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
    socket.on('offer_accepted', (data) => {
        addOffer(data, 'offer_accepted');
        showAcceptedOffer(data);
    });
}

document.querySelector('#payment-modal-dismiss').addEventListener('click', closeAcceptedOfferModal);
document.querySelector('#payment-modal-confirm').addEventListener('click', closeAcceptedOfferModal);

form.addEventListener('submit', (event) => {
    event.preventDefault();

    const accountId = userIdInput.value.trim();
    const accountType = roomTypeInput.value;

    if (accountId) {
        const url = new URL(window.location.href);
        url.searchParams.set('account_type', accountType);
        url.searchParams.set('account_id', accountId);
        window.history.replaceState({}, '', url);
        connectToRoom(accountType, accountId);
    }
});

roomTypeInput.addEventListener('change', () => {
    const isWensh = roomTypeInput.value === 'wensh';
    roomIdLabel.textContent = isWensh ? 'معرف الونش' : 'معرف المستخدم';
    userIdInput.placeholder = isWensh ? 'مثال: 1' : 'مثال: 12';
});

const pageParameters = new URLSearchParams(window.location.search);
const accountIdFromUrl = pageParameters.get('account_id') ?? pageParameters.get('user_id');
const accountTypeFromUrl = pageParameters.get('account_type') ?? 'user';

if (accountTypeFromUrl === 'wensh') {
    roomTypeInput.value = 'wensh';
    roomTypeInput.dispatchEvent(new Event('change'));
}

if (accountIdFromUrl) {
    userIdInput.value = accountIdFromUrl;
    connectToRoom(accountTypeFromUrl, accountIdFromUrl);
}
