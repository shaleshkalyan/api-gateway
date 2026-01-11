import { Request, Response, NextFunction } from "express";
import { AppError } from "../utils/error";
import { logger } from "../utils/logger";

export const errorMiddleware = (
  err: Error,
  req: Request,
  res: Response,
  _next: NextFunction
) => {
  logger.error("Unhandled API error", {
    path: req.path,
    error: err.message,
  });

  if (err instanceof AppError) {
    return res.status(err.statusCode).json({
      success: false,
      message: err.message,
    });
  }

  res.status(500).json({
    success: false,
    message: "Internal server error",
  });
};
