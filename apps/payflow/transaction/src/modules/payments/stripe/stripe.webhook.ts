// import { stripe } from "./stripe.client";
// import axios from "axios";
// import { logger } from "../../../utils/logger";

// export async function stripeWebhook(req: Request, res: Response) {
//   const sig = req.headers["stripe-signature"];

//   const event = stripe.webhooks.constructEvent(
//     req.body,
//     sig!,
//     process.env.STRIPE_WEBHOOK_SECRET!
//   );

//   if (event.type === "payment_intent.succeeded") {
//     const intent = event.data.object as any;

//     const paymentId = intent.metadata.payment_id;
//     const userId = intent.metadata.user_id;
//     const amount = intent.amount_received / 100;

//     logger.info("Stripe payment succeeded", {
//       payment_id: paymentId,
//     });

//     const transactionResponse = await axios.post(`${process.env.SELF_BASE_URL}/transaction/create`, {
//       user_id: userId,
//       amount,
//       currency: intent.currency.toUpperCase(),
//       type: "credit",
//       idempotency_key: paymentId,
//     });
//   }
//   return true;
// }
