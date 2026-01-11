import {
  Entity,
  PrimaryGeneratedColumn,
  Column,
  CreateDateColumn,
} from "typeorm";

@Entity("payflow_users")
export class User {
  @PrimaryGeneratedColumn("uuid")
  id!: string;

  @Column({ unique: true })
  email!: string;

  @Column()
  password!: string;

  @Column("text", { array: true, default: [] })
  roles!: string[];

  @Column("text", { array: true, default: [] })
  permissions!: string[];

  @CreateDateColumn()
  created_at!: Date;
}
