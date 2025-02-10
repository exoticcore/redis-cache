import { createUser, deleteUser, getUserByEmail, updateUser } from "./user.service.js";

export const getUserByEmailHandler = async (req, res) => {
    const { email } = req.params;
    try {
        const user = await getUserByEmail(email);

        if (!user) {
            return res.status(404).json({ message: 'user not found' });
        }

        return res.status(200).json(user);

    } catch (err) {
        return res.status(500).json({ message: 'internal server error' });
    }
};

export const createUserHandler = async (req, res) => {
    const { email, name } = req.body;
    if (!email.length || !name.length) {
        return res.status(400).json({ message: 'email and name are required' });
    }
    try {
        const created = await createUser(email, name);

        if (!created) return res.status(409).json({ message: 'user already exists' });

        return res.status(201).json({ message: 'user created' });

    } catch (err) {
        console.log(err);
        return res.status(500).json({ message: 'internal server error' });
    }
};

export const updateUserHandler = async (req, res) => {
    const { email } = req.params;
    const { name } = req.body;
    if (!email.length || !name.length) {
        return res.status(400).json({ message: 'email and name are required' });
    }

    try {
        const updated = await updateUser(email, name);

        if (!updated) return res.status(404).json({ message: 'user not found' });

        return res.status(200).json({ message: 'user updated' });

    } catch (err) {

        console.log(err);
        return res.status(500).json({ message: 'internal server error' });
    }
};

export const deleteUserHandler = async (req, res) => {
    const { email } = req.params;
    if (!email || !email.length) {
        return res.status(400).json({ message: 'email is required' });
    }

    try {
        const deleted = await deleteUser(email);

        if (!deleted) return res.status(404).json({ message: 'user not found' });

        return res.status(200).json({ message: 'user deleted' });

    } catch (err) {
        return res.status(500).json({ message: 'internal server error' });
    }
};