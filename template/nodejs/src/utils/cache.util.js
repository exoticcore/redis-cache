import { TTL_CACHE } from "../config/constant.js";
import { redisClient } from "../config/redis.js";

export const getCache = async (key) => {
    try {
        if (!redisClient.isReady) throw new Error("Redis not ready");
        const data = await redisClient.get(key);
        return data ?? false;
    } catch (error) {
        // console.warn(`⚠️ Redis error in getCache: ${error.message}`);
        return false; // ให้ API ไปดึงฐานข้อมูลเอง
    }
};

export const setCache = async (key, value, ttl = TTL_CACHE) => {
    try {
        if (!redisClient.isReady) throw new Error("Redis not ready");
        await redisClient.set(key, value, { EX: ttl });
    } catch (error) {
        console.warn(`⚠️ Redis error in setCache: ${error.message}`);
    }
};

export const deleteCache = async (key) => {
    try {
        if (!redisClient.isReady) throw new Error("Redis not ready");
        await redisClient.del(key);
    } catch (error) {
        console.warn(`⚠️ Redis error in deleteCache: ${error.message}`);
    }
};

export const increaseVersion = async (key) => {
    try {
        if (!redisClient.isReady) throw new Error("Redis not ready");
        await redisClient.incr(key);
    } catch (error) {
        console.warn(`⚠️ Redis error in increaseVersion: ${error.message}`);
    }
};

export const redisHealthCheck = async () => {
    try {
        if (!redisClient.isReady) throw new Error("Redis not ready");
        return await redisClient.ping();
    } catch (error) {
        console.warn(`⚠️ Redis health check failed: ${error.message}`);
        return false;
    }
};
