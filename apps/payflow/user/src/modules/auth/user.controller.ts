import { Request, Response, NextFunction } from "express";
import { AuthService } from "./user.service";
import { validateAuth } from "./user.validator";
import { success } from "../../utils/response";

const service = new AuthService();

export const register = async (req: Request, res: Response, next: NextFunction) => {
  try {
    validateAuth(req.body);
    res.json(success(await service.register(req.body.email, req.body.password)));
  } catch (e) {
    next(e);
  }
};

export const login = async (req: Request, res: Response, next: NextFunction) => {
  try {
    validateAuth(req.body);
    res.json(success(await service.login(req.body.email, req.body.password)));
  } catch (e) {
    next(e);
  }
};
