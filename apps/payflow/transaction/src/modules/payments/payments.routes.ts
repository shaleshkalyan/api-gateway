import { Router } from "express";
import { initiatePayment } from "./payments.controller";
import { stripeWebhook } from "./stripe/stripe.webhook";

const router = Router();

router.post("/payment/initiate", initiatePayment);
// router.post("/payment/webhook/stripe", stripeWebhook);

export default router;
