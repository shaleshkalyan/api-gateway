import { Request, Response, NextFunction } from "express";
import { StripeService } from "./stripe/stripe.service";
import { success } from "../../utils/response";
import { AppError } from "../../utils/error";

const stripeService = new StripeService();

export async function initiatePayment(req: Request, res: Response, next: NextFunction) {
  try {
    const { user_id, amount, currency, provider } = req.body;

    if (provider !== "STRIPE") {
      throw new AppError("Unsupported provider", 400);
    }

    const result = await stripeService.initiatePayment({
      user_id,
      amount,
      currency,
    });

    res.json(success(result));
  } catch (err) {
    next(err);
  }
}
