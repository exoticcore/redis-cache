import express from 'express';

import brandRoutes from '../api/brand/brand.route.js';
import categoryRutes from '../api/category/category.route.js';
import productRoutes from '../api/product/product.route.js';

const router = express.Router();

router.use('/product', productRoutes);
router.use(`/category`, categoryRutes);
router.use(`/brand`, brandRoutes);

export default router;