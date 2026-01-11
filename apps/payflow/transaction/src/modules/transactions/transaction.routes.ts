import { Router } from "express";
import { create } from "./transaction.controller";

const router = Router();
router.post("/transaction/create", create);
export default router;
