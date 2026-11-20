import express from 'express';
import http from 'http';
import { Server } from 'socket.io';

const app = express();
const server = http.createServer(app);

app.use(express.json());

const io = new Server(server, {
    cors: {
        origin: '*'
    }
});


io.on('connection', (socket) => {

    const room = socket.handshake.auth.room;

    socket.join(room);

    console.log(`Connected to room: ${room}`);

    socket.on('disconnect', () => {
        console.log('Disconnected:', socket.id);
    });

});

app.post('/send-event', (req, res) => {

    const { room, event, data } = req.body;

    if (!room || !event) {
        return res.status(400).json({
            success: false,
            message: 'Room and event are required'
        });
    }

    io.to(room).emit(event, data);

    return res.json({
        success: true,
        message: 'Event sent successfully'
    });
});

server.listen(3000, () => {
    console.log('Server running on port 3000');
});
