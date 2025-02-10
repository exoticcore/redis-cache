import { createCategory, deleteCategory, getAllCategories, updateCategory } from "./category.service.js";

export const getAllCategoriesHandler = async (req, res) => {
    try {
        const categories = await getAllCategories();
        return res.status(200).json(categories);
    } catch (err) {
        return res.status(500).json({ message: 'internal server error' });
    }
};

export const createCategoryHandler = async (req, res) => {
    const { name } = req.body;
    if (!name || !name.length) {
        return res.status(400).json({ message: 'name is required' });
    }
    try {
        const created = await createCategory(name);
        if (!created) return res.status(500).json({ message: 'failed to create category' });

        return res.status(201).json({ message: 'category created' });
    } catch (err) {
        return res.status(500).json({ message: 'internal server error' });
    }
};

export const updateCategoryHandler = async (req, res) => {
    const { id } = req.params;
    const { name } = req.body;

    if (!name || !name.length || !id || !parseInt(id)) {
        return res.status(400).json({ message: 'id and name are required' });
    }

    try {
        const updated = await updateCategory(id, name);

        if (!updated) return res.status(404).json({ message: 'category not found' });

        return res.status(200).json({ message: 'category updated' });
    } catch (err) {
        return res.status(500).json({ message: 'internal server error' });
    }
};

export const deleteCategoryHandler = async (req, res) => {
    const { id } = req.params;

    if (!id || !parseInt(id)) {
        return res.status(400).json({ message: 'id is required' });
    }

    try {
        const deleted = await deleteCategory(id);

        if (!deleted) return res.status(404).json({ message: 'category not found' });

        return res.status(200).json({ message: 'category deleted' });
    } catch (err) {
        console.log(err);
        return res.status(500).json({ message: 'internal server error' });
    }
};