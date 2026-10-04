import { Router, type IRouter } from "express";
import healthRouter from "./health";
import auditRouter from "./audit";

const router: IRouter = Router();

router.use(healthRouter);
router.use(auditRouter);

export default router;
