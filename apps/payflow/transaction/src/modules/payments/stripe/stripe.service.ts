import { stripe } from "./stripe.client";
import { randomUUID } from "crypto";
import { logger } from "../../../utils/logger";

export class StripeService {
  async initiatePayment(body: {
    user_id: string;
    amount: number;
    currency: string;
  }) {
    const paymentId = randomUUID();

    logger.info("Stripe payment initiated", {
      payment_id: paymentId,
      user_id: body.user_id,
      amount: body.amount,
    });

    const intent = await stripe.paymentIntents.create({
      amount: body.amount * 100,
      currency: body.currency.toLowerCase(),
      metadata: {
        payment_id: paymentId,
        user_id: body.user_id,
      },
    });

    return {
      payment_id: paymentId,
      provider_payload: {
        client_secret: intent.client_secret,
      },
    };
  }
}
