import { z } from 'zod';

// We're keeping a simple non-relational schema here.
// IRL, you will have a schema for your data models.
export const citaSchema = z.object({
    id: z.string(),
    id_cliente: z.number(),
    fecha_hora_cita: z.string(),
    estado: z.number(),
    cliente: z.object({
        nombre: z.string(),
        apellido: z.string(),
        email: z.string(),
        telefono: z.string(),
        direccion: z.string(),
        estado: z.number(),
    }),
});

export type Cita = z.infer<typeof citaSchema>;
