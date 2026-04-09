import { NestFactory } from '@nestjs/core';
import { ValidationPipe } from '@nestjs/common';
import { AppModule } from './app.module';

async function bootstrap() {
  const app = await NestFactory.create(AppModule);

  app.setGlobalPrefix('api');
  app.useGlobalPipes(
    new ValidationPipe({
      whitelist: true,
      forbidNonWhitelisted: true,
      transform: true,
    }),
  );
  app.enableCors();

  // Must match nginx upstream (nginx.conf). Railway sets PORT for the public listener (nginx on 80),
  // so binding Nest to PORT would leave nothing on 3000 and /api/* would 502.
  const port = parseInt(process.env.NEST_LISTEN_PORT ?? '3000', 10);
  await app.listen(port, '0.0.0.0');
  console.log(`Server running on port ${port}`);
}

bootstrap();
