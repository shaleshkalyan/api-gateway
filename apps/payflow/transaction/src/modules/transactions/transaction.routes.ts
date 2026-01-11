import { Router } from "express";
import { create, transactionStatus } from "./transaction.controller";

const router = Router();
router.post("/transaction/create", create);
router.post("/transaction/status", transactionStatus);
export default router;
