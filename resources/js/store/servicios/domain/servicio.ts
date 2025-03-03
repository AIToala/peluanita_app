import { z } from 'zod';

// We're keeping a simple non-relational schema here.
// IRL, you will have a schema for your data models.
export const servicioSchema = z.object({
    id: z.string(),
    nombre: z.string(),
    descripcion: z.string(),
    estado: z.string(),
});

export type Servicio = z.infer<typeof servicioSchema>;
