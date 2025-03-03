import { z } from 'zod';

// We're keeping a simple non-relational schema here.
// IRL, you will have a schema for your data models.
export const clienteSchema = z.object({
    id_usuario: z.string(),
    nombre: z.string(),
    apellido: z.string(),
    email: z.string(),
    telefono: z.string(),
    direccion: z.string(),
    estado: z.string(),
    usuario: z.object({
        estado: z.number(),
    }),
});

export type Cliente = z.infer<typeof clienteSchema>;
