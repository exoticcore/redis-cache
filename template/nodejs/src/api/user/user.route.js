import express from 'express';
import { createUserHandler, deleteUserHandler, getUserByEmailHandler, updateUserHandler } from './user.controller.js';

const router = express.Router();

router.get('/:email', getUserByEmailHandler);
router.post('/', createUserHandler);
router.put('/:email', updateUserHandler);
router.delete('/:email', deleteUserHandler);


export default router;