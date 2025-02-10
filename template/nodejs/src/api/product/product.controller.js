import { createProduct, deleteProduct, getAllProducts, getProductByID, updateProduct } from "./product.service.js";

export const getAllProductsHandler = async (req, res) => {
    const page = parseInt(req.query.page) || 1;
    const limit = parseInt(req.query.limit) || 10;

    if (page < 1 || limit < 1) {
        return res.status(400).json({ message: 'invalid page or limit' });
    }

    try {
        const products = await getAllProducts(page, limit);
        return res.status(200).json(products);
    } catch (err) {
        return res.status(500).json({ message: 'internal server error' });
    }
};

export const getProductByIDHandler = async (req, res) => {
    const { id } = req.params;
    if (!id || !parseInt(id)) {
        return res.status(400).json({ message: 'id is required' });
    }
    try {
        const product = await getProductByID(id);

        if (!product) return res.status(404).json({ message: 'product not found' });

        return res.status(200).json(product);
    } catch (err) {
        return res.status(500).json({ message: 'internal server error' });
    }
};

export const createProductHandler = async (req, res) => {
    const { name, description, price, brand_id, category_id } = req.body;
    if (!name || !name.length || !description || !description.length || !price || !parseFloat(price) || !brand_id || !parseInt(brand_id) || !category_id || !parseInt(category_id)) {
        return res.status(400).json({ message: 'name, description, price, brand_id, and category_id are required' });
    }
    try {
        const created = await createProduct({ name, description, price, brand_id, category_id });
        if (!created) return res.status(500).json({ message: 'failed to create product' });

        return res.status(201).json({ message: 'product created' });
    } catch (err) {
        console.log(err);
        return res.status(500).json({ message: 'internal server error' });
    }
};

export const updateProductHandler = async (req, res) => {
    const { id } = req.params;
    if (!id || !parseInt(id)) {
        return res.status(400).json({ message: 'id is required' });
    }

    const { name, description } = req.body;
    if (!name && !description) {
        return res.status(400).json({ message: 'name or description is required' });
    }

    const updateData = { name, description };

    try {
        const updated = await updateProduct(id, updateData);

        if (!updated) return res.status(404).json({ message: 'product not found' });

        return res.status(200).json({ message: 'product updated' });
    } catch (err) {
        return res.status(500).json({ message: 'internal server error' });
    }
};

export const deleteProductHandler = async (req, res) => {
    const { id } = req.params;
    if (!id || !parseInt(id)) {
        return res.status(400).json({ message: 'id is required' });
    }
    try {
        const deleted = await deleteProduct(id);

        if (!deleted) return res.status(404).json({ message: 'product not found' });

        return res.status(200).json({ message: 'product deleted' });
    } catch (err) {
        return res.status(500).json({ message: 'internal server error' });
    }
};