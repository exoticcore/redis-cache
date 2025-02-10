import { createBrand, deleteBrand, getAllBrands, updateBrand } from "./brand.service.js";

export const getAllBrandsHandler = async (req, res) => {
    try {
        const brands = await getAllBrands();
        return res.status(200).json(brands);
    } catch (err) {
        return res.status(500).json({ message: 'internal server error' });
    }
};

export const createBrandHandler = async (req, res) => {
    const { name } = req.body;
    if (!name || !name.length) {
        return res.status(400).json({ message: 'name is required' });
    }

    try {
        const created = await createBrand(name);

        if (!created) return res.status(500).json({ message: 'failed to create brand' });

        return res.status(201).json({ message: 'brand created' });
    } catch (err) {
        return res.status(500).json({ message: 'internal server error' });
    }
};

export const updateBrandHandler = async (req, res) => {
    const { id } = req.params;
    const { name } = req.body;

    if (!name || !name.length || !id || !parseInt(id)) {
        return res.status(400).json({ message: 'id and name are required' });
    }

    try {
        const updated = await updateBrand(id, name);

        if (!updated) return res.status(404).json({ message: 'brand not found' });

        return res.status(200).json({ message: 'brand updated' });
    } catch (err) {
        return res.status(500).json({ message: 'internal server error' });
    }
};

export const deleteBrandHandler = async (req, res) => {
    const { id } = req.params;

    if (!id || !parseInt(id)) {
        return res.status(400).json({ message: 'id is required' });
    }

    try {
        const deleted = await deleteBrand(id);

        if (!deleted) return res.status(404).json({ message: 'brand not found' });

        return res.status(200).json({ message: 'brand deleted' });
    } catch (err) {
        return res.status(500).json({ message: 'internal server error' });
    }
};