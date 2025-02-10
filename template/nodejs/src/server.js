import express from 'express';
import router from './routes/route.js';
import { PORT } from './config/constant.js';

const app = express();

app.use(express.json());
app.use(express.urlencoded({ extended: true }));

app.use('/api/v1', router);

app.use((req, res) => {
    res.status(404).json({ message: 'route not found' });
});

app.listen(PORT, () => {
    console.log(`Server is running on port ${PORT}`);
});