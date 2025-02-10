import express from 'express';
import { createCategoryHandler, deleteCategoryHandler, getAllCategoriesHandler, updateCategoryHandler } from './category.controller.js';

const router = express.Router();
const path = '/category';

router.get('/', getAllCategoriesHandler);
router.post('/', createCategoryHandler);
router.put('/:id', updateCategoryHandler);
router.delete('/:id', deleteCategoryHandler);


export default router;