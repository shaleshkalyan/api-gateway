import { Router } from "express";
import { createWallet, ledgerEntry } from "./accounting.controller";

const router = Router();

router.post("/wallet/create", createWallet);
router.post("/ledger/entry", ledgerEntry);

export default router;
