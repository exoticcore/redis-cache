import { mysql } from "../../config/database.js";
import { getCache, increaseVersion, setCache } from "../../utils/cache.util.js";

export const getAllCategories = async () => {
    const categoryVersion = await getCache('category:version') || '0';
    const cacheKey = `category:list:${categoryVersion}`;

    const cachedCategories = await getCache(cacheKey);
    if (cachedCategories) {
        console.log(`Cache hit for key: ${cacheKey}`);
        return JSON.parse(cachedCategories);
    }
    console.log(`Cache miss for key: ${cacheKey}`);

    const [rows] = await mysql.query(`SELECT * FROM category`);

    if (rows.length) {
        await setCache(cacheKey, JSON.stringify(rows));
        return rows;
    }

    return [];
};

export const createCategory = async (name) => {
    const [result] = await mysql.query('INSERT INTO category (name) VALUES (?)', [name]);

    if (result.affectedRows) {
        await increaseVersion('category:version');
        return true;
    }

    return false;
};

export const updateCategory = async (id, name) => {
    const isCategory = await getCategoryById(id);
    if (!isCategory) return false;

    const [result] = await mysql.query('UPDATE category SET name = ? WHERE id = ?', [name, id]);

    if (result.affectedRows) {
        await increaseVersion('category:version');
        return true;
    }

    return false;
};

export const deleteCategory = async (id) => {
    const isCategory = await getCategoryById(id);
    if (!isCategory) return false;

    const [result] = await mysql.query('DELETE FROM category WHERE id = ?', [id]);

    if (result.affectedRows) {
        await increaseVersion('category:version');
        return true;
    }

    return false;
};

const getCategoryById = async (id) => {
    const [rows] = await mysql.query('SELECT * FROM category WHERE id = ?', [id]);
    if (rows.length) {
        return rows[0];
    }

    return null;
};