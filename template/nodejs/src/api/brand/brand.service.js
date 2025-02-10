import { mysql } from "../../config/database.js";
import { getCache, increaseVersion, setCache } from "../../utils/cache.util.js";

export const getAllBrands = async () => {
    const brandVersion = await getCache('brand:version') || '0';
    const cacheKey = `brand:list:${brandVersion}`;

    const cachedBrands = await getCache(cacheKey);
    if (cachedBrands) {
        console.log(`Cache hit for key: ${cacheKey}`);
        return JSON.parse(cachedBrands);
    }
    console.log(`Cache miss for key: ${cacheKey}`);

    const [rows] = await mysql.query(
        `SELECT * FROM brand
        ORDER BY id DESC`
    );

    if (rows.length) {
        await setCache(cacheKey, JSON.stringify(rows));
        return rows;
    }

    return [];
};

export const createBrand = async (name) => {
    const [result] = await mysql.query('INSERT INTO brand (name) VALUES (?)', [name]);

    if (result.affectedRows) {
        await increaseVersion('brand:version');
        return true;
    }

    return false;
};

export const updateBrand = async (id, name) => {
    const isBrand = await getBrandById(id);
    if (!isBrand) return false;

    const [result] = await mysql.query('UPDATE brand SET name = ? WHERE id = ?', [name, id]);

    if (result.affectedRows) {
        await increaseVersion('brand:version');
        return true;
    }

    return false;
};

export const deleteBrand = async (id) => {
    const isBrand = await getBrandById(id);
    if (!isBrand) return false;

    const [result] = await mysql.query('DELETE FROM brand WHERE id = ?', [id]);

    if (result.affectedRows) {
        await increaseVersion('brand:version');
        return true;
    }

    return false;
};

const getBrandById = async (id) => {
    const [rows] = await mysql.query('SELECT * FROM brand WHERE id = ?', [id]);
    if (rows.length) {
        return rows[0];
    }

    return null;
};