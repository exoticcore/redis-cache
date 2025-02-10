import { createClient } from "redis";
import { REDIS_PASSWORD, REDIS_URL } from "./constant.js";

export const redisClient = createClient({
    url: REDIS_URL,
    password: REDIS_PASSWORD,
    socket: {
        connectTimeout: 5000, // 5 seconds
        reconnectStrategy: (retries) => {
            // console.warn(`🔄 Redis reconnect attempt #${retries}`);
            return Math.min(retries * 1000, 5000); // 5 seconds max
        },
    },
});

// Event Handling
redisClient.on("ready", () => console.log("✅ Redis is ready"));
redisClient.on("error", (err) => console.error("❌ Redis error:", err.message));
redisClient.on("reconnecting", () => console.log("🔄 Reconnecting to Redis..."));
redisClient.on("end", () => console.warn("⚠️ Redis connection closed"));

(async () => {
    try {
        await redisClient.connect();
    } catch (err) {
        console.warn("⚠️ Failed to connect to Redis:", err.message);
    }
})();
