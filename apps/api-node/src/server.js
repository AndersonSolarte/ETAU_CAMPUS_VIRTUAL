import { createServer } from "node:http";

import { Server as SocketIOServer } from "socket.io";

import { createApp } from "./app.js";

const app = createApp();
const server = createServer(app);
const io = new SocketIOServer(server, {
  cors: {
    origin: "*"
  }
});

io.on("connection", (socket) => {
  socket.emit("platform:ready", {
    service: "tau-api",
    timestamp: new Date().toISOString()
  });
});

const port = Number(process.env.PORT || 4000);

server.listen(port, () => {
  console.log(`TAU API listening on http://localhost:${port}`);
});

