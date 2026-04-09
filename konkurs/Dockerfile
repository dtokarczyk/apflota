# --- Stage 1: Build frontend ---
FROM node:20-alpine AS frontend-build
WORKDIR /build
COPY frontend/package.json frontend/package-lock.json* ./
RUN npm install
COPY frontend/ ./
RUN npm run build

# --- Stage 2: Build backend ---
FROM node:20-alpine AS backend-build
WORKDIR /build
COPY backend/package.json backend/package-lock.json* ./
RUN npm install
COPY backend/ ./
RUN npm run build

# --- Stage 3: Production ---
FROM node:20-alpine AS production

RUN apk add --no-cache nginx

WORKDIR /app

COPY --from=backend-build /build/dist ./dist
COPY --from=backend-build /build/node_modules ./node_modules
COPY --from=backend-build /build/package.json ./

COPY --from=frontend-build /build/dist /usr/share/nginx/html

COPY nginx.conf /etc/nginx/nginx.conf
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

RUN mkdir -p /run/nginx

EXPOSE 80

CMD ["/entrypoint.sh"]
