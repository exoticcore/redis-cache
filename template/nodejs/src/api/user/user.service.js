import { mysql } from "../../config/database.js";
import { deleteCache, getCache, setCache } from "../../utils/cache.util.js";

export const getUserByEmail = async (email) => {
    const cacheKey = `user:email:${email}`;
    const cachedUser = await getCache(cacheKey);

    if (cachedUser) {
        console.log(`Cache hit for key: ${cacheKey}`);
        return JSON.parse(cachedUser);
    }
    console.log(`Cache miss for key: ${cacheKey}`);

    const [rows] = await mysql.query('SELECT * FROM users WHERE email = ?', [email]);

    const user = rows[0];

    if (user) {
        await setCache(cacheKey, JSON.stringify(user));
        return user;
    }
    return null;
};

export const createUser = async (email, name) => {
    const isUser = await getUserByEmail(email);

    if (isUser) return false;

    const [result] = await mysql.query('INSERT INTO users (email, name) VALUES (?, ?)', [email, name]);

    if (result.affectedRows) {
        const [rows] = await mysql.query('SELECT * FROM users WHERE id = ?', [result.insertId]);
        const user = rows[0];
        await setCache(`user:email:${email}`, JSON.stringify(user));
        return true;
    }

    return false;
};

export const updateUser = async (email, name) => {
    const isUser = await getUserByEmail(email);
    if (!isUser) return false;

    const [result] = await mysql.query('UPDATE users SET name = ? WHERE email = ?', [name, email]);

    if (result.affectedRows) {
        const [rows] = await mysql.query('SELECT * FROM users WHERE email = ?', [email]);
        const user = rows[0];
        await setCache(`user:email:${email}`, JSON.stringify(user));
        return true;
    }

    return false;
};

export const deleteUser = async (email) => {
    const isUser = await getUserByEmail(email);
    if (!isUser) return false;

    const [result] = await mysql.query('DELETE FROM users WHERE email = ?', [email]);

    if (result.affectedRows) {
        await deleteCache(`user:email:${email}`);
        return true;
    }

    return false;
};