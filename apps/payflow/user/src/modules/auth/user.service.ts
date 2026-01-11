import bcrypt from "bcrypt";
import { AppDataSource } from "../../data-source";
import { User } from "./user.entity";
import { AppError } from "../../utils/error";
import { signToken } from "../../utils/jwt";

export class AuthService {
  private repo = AppDataSource.getRepository(User);

  async register(email: string, password: string) {
    const exists = await this.repo.findOne({ where: { email } });
    if (exists) throw new AppError("User exists", 409);

    const hashed = await bcrypt.hash(password, 10);
    const user = this.repo.create({
      email,
      password: hashed,
      roles: ["user"],
      permissions: [],
    });

    await this.repo.save(user);
    return { user_id: user.id };
  }

  async login(email: string, password: string) {
    const user = await this.repo.findOne({ where: { email } });
    if (!user) throw new AppError("Invalid credentials", 401);

    const valid = await bcrypt.compare(password, user.password);
    if (!valid) throw new AppError("Invalid credentials", 401);

    return {
      access_token: signToken({
        sub: user.id,
        roles: user.roles,
        permissions: user.permissions,
      }),
    };
  }
}
