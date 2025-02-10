import { mysql } from "../../config/database.js";
import { getCache, increaseVersion, redisHealthCheck, setCache } from "../../utils/cache.util.js";

export const getAllProducts = async (page = 1, limit = 10) => {
    const offset = (page - 1) * limit;

    const healthCheck = await redisHealthCheck();
    if (!healthCheck) {
        console.log('1.Query command logic to database');
        console.log('2.Response result !!');
    } else {
        console.log('1.Get cache or get data from database');
        console.log('2.Business logic and response result !!');
    }

    const productVersion = await getCache('product:version') || '0';
    const categoryVersion = await getCache('category:version') || '0';

    const cacheKey = `product:list:page:${page}:limit:${limit}:version:${productVersion}:${categoryVersion}`;
    const cachedProducts = await getCache(cacheKey);
    if (cachedProducts) {
        console.log(`Cache hit for key: ${cacheKey}`);
        return JSON.parse(cachedProducts);
    }
    console.log(`Cache miss for key: ${cacheKey}`);



    const [rows] = await mysql.query(
        `SELECT p.*, c.name as category_name
        FROM product p
        JOIN category c ON p.category_id = c.id
        ORDER BY p.id DESC
        LIMIT ?, ?`,
        [offset, limit]
    );

    if (rows.length) {
        await setCache(cacheKey, JSON.stringify(rows));
        return rows;
    }

    return [];
};

export const getProductByID = async (productId) => {
    const productVersion = await getCache('product:version') || '0';
    const categoryVersion = await getCache('category:version') || '0';
    const brandVersion = await getCache('brand:version') || '0';

    const cacheKey = `product:id:${productId}:version:${productVersion}:${categoryVersion}:${brandVersion}`;

    const cachedProduct = await getCache(cacheKey);
    if (cachedProduct) {
        console.log(`Cache hit for key: ${cacheKey}`);
        return JSON.parse(cachedProduct);
    }
    console.log(`Cache miss for key: ${cacheKey}`);

    const [rows] = await mysql.query(
        `SELECT p.*, c.name as category_name, b.name as brand_name
        FROM product p
        JOIN category c ON p.category_id = c.id
        JOIN brand b ON p.brand_id = b.id
        WHERE p.id = ?`,
        [productId]
    );

    if (rows.length && rows[0]) {
        await setCache(cacheKey, JSON.stringify(rows[0]));
        return rows[0];
    }

    return null;
};

export const createProduct = async (data) => {
    const [result] = await mysql.query(
        `INSERT INTO product (name, description, price, category_id, brand_id)
            VALUES (?, ?, ?, ?, ?)`,
        [data.name, data.description, data.price, data.category_id, data.brand_id]
    );

    if (result.affectedRows) {
        await increaseVersion('product:version');
        return true;
    }

    return false;
};

export const updateProduct = async (productId, data) => {
    const isProduct = await selectProductFromId(productId);
    if (!isProduct) return null;

    const productName = data?.name || isProduct.name;
    const productDesc = data?.description || isProduct.description;

    const [result] = await mysql.query(
        `UPDATE product SET name = ?, description = ? WHERE id = ?`,
        [productName, productDesc, productId]
    );

    if (result.affectedRows) {
        await increaseVersion('product:version');
        return true;
    }

    return false;
};

export const deleteProduct = async (productId) => {
    const isProduct = await selectProductFromId(productId);
    if (!isProduct) return null;

    const [result] = await mysql.query(
        `DELETE FROM product WHERE id = ?`,
        [productId]
    );

    if (result.affectedRows) {
        await increaseVersion('product:version');
        return true;
    }

    return false;
};

const selectProductFromId = async (productId) => {
    const [rows] = await mysql.query(
        `SELECT * FROM product WHERE id = ?`,
        [productId]
    );

    if (rows.length && rows[0]) {
        return rows[0];
    }

    return null;

};