const PORT = process.env.PORT || 3034;

// mysql connection
const DB_HOST = process.env.DB_HOST || 'localhost';
const DB_USER = process.env.DB_USER || 'root';
const DB_PASSWORD = process.env.DB_PASSWORD || 'secret';
const DB_NAME = process.env.DB_NAME || 'rediscasedb';
const DB_PORT = parseInt(process.env.DB_PORT) || 3308;

// redis connection
const REDIS_URL = process.env.REDIS_URL || 'redis://localhost:6380';
const REDIS_PASSWORD = process.env.REDIS_PASSWORD || 'secretpwd';
const TTL_CACHE = parseInt(process.env.TTL_CACHE) || 3600;

export {
    PORT,
    DB_HOST,
    DB_USER,
    DB_PASSWORD,
    DB_NAME,
    DB_PORT,
    REDIS_URL,
    REDIS_PASSWORD,
    TTL_CACHE
};