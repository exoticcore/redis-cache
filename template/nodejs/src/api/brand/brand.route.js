import express from 'express';
import { createBrandHandler, deleteBrandHandler, getAllBrandsHandler, updateBrandHandler } from './brand.controller.js';

const router = express.Router();

router.get('/', getAllBrandsHandler);
router.post('/', createBrandHandler);
router.put('/:id', updateBrandHandler);
router.delete('/:id', deleteBrandHandler);

export default router;