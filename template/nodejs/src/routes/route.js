import express from "express";

import userRoutes from "../api/user/user.route.js";
import catalogRoutes from './catalog.route.js';

const router = express.Router();

router.use('/user', userRoutes);
router.use('/catalog', catalogRoutes);

export default router;