import express from 'express';
import { createProductHandler, deleteProductHandler, getAllProductsHandler, getProductByIDHandler, updateProductHandler } from './product.controller.js';

const router = express.Router();

router.get('/', getAllProductsHandler);
router.get('/:id', getProductByIDHandler);
router.post('/', createProductHandler);
router.put('/:id', updateProductHandler);
router.delete('/:id', deleteProductHandler);

export default router;