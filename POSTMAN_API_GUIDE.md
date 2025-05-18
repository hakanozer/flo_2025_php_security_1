# Postman API Endpoints Guide

This document provides information about the available API endpoints in the application and how to test them using Postman.

## Base URL

All API endpoints are prefixed with `/api`. If you're running the application locally, the base URL would be:

```
http://localhost:8000/api
```

## Available Endpoints

### 1. Register

**Endpoint:** `POST /api/register`

**Description:** Registers a new user and returns a JWT token.

**Request Body:**
```json
{
    "name": "New User",
    "email": "newuser@example.com",
    "password": "password",
    "password_confirmation": "password",
    "role": "note"
}
```

> **Note:** The `role` field must be either "note" or "product". This determines what services the user can access.

**Response:**
```json
{
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "token_type": "bearer",
    "expires_in": 3600,
    "user": {
        "id": 1,
        "name": "New User",
        "email": "newuser@example.com",
        "email_verified_at": null,
        "role": "note",
        "created_at": "2023-01-01T00:00:00.000000Z",
        "updated_at": "2023-01-01T00:00:00.000000Z"
    }
}
```

### 2. Login

**Endpoint:** `POST /api/login`

**Description:** Authenticates a user and returns a JWT token.

**Request Body:**
```json
{
    "email": "user@example.com",
    "password": "password"
}
```

**Response:**
```json
{
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "token_type": "bearer",
    "expires_in": 3600,
    "user": {
        "id": 1,
        "name": "User Name",
        "email": "user@example.com",
        "email_verified_at": null,
        "role": "note",
        "created_at": "2023-01-01T00:00:00.000000Z",
        "updated_at": "2023-01-01T00:00:00.000000Z"
    }
}
```

### 3. Logout

**Endpoint:** `POST /api/logout`

**Description:** Logs out the authenticated user by invalidating their token.

**Headers:**
```
Authorization: Bearer {your_jwt_token}
```

**Response:**
```json
{
    "message": "Successfully logged out"
}
```

### 4. Get User Information

**Endpoint:** `GET /api/user`

**Description:** Returns information about the authenticated user.

**Headers:**
```
Authorization: Bearer {your_jwt_token}
```

**Response:**
```json
{
    "id": 1,
    "name": "User Name",
    "email": "user@example.com",
    "email_verified_at": null,
    "role": "note",
    "created_at": "2023-01-01T00:00:00.000000Z",
    "updated_at": "2023-01-01T00:00:00.000000Z"
}
```

### 5. Notes API (Note Role Only)

**Endpoints:** 
- `GET /api/notes` - List all notes
- `POST /api/notes` - Create a new note
- `GET /api/notes/{id}` - Get a specific note
- `PUT /api/notes/{id}` - Update a note
- `DELETE /api/notes/{id}` - Delete a note

**Description:** These endpoints allow users with the "note" role to manage their notes.

**Headers:**
```
Authorization: Bearer {your_jwt_token}
```

**Request Body (for POST and PUT):**
```json
{
    "title": "My Note Title",
    "content": "This is the content of my note."
}
```

**Response (for GET, POST, PUT):**
```json
{
    "id": 1,
    "user_id": 1,
    "title": "My Note Title",
    "content": "This is the content of my note.",
    "created_at": "2023-01-01T00:00:00.000000Z",
    "updated_at": "2023-01-01T00:00:00.000000Z"
}
```

### 6. Products API (Product Role Only)

**Endpoints:** 
- `GET /api/products` - List all products
- `POST /api/products` - Create a new product
- `GET /api/products/{id}` - Get a specific product
- `PUT /api/products/{id}` - Update a product
- `DELETE /api/products/{id}` - Delete a product

**Description:** These endpoints allow users with the "product" role to manage their products.

**Headers:**
```
Authorization: Bearer {your_jwt_token}
```

**Request Body (for POST and PUT):**
```json
{
    "name": "Product Name",
    "description": "This is the description of my product.",
    "price": 99.99
}
```

**Response (for GET, POST, PUT):**
```json
{
    "id": 1,
    "user_id": 1,
    "name": "Product Name",
    "description": "This is the description of my product.",
    "price": "99.99",
    "created_at": "2023-01-01T00:00:00.000000Z",
    "updated_at": "2023-01-01T00:00:00.000000Z"
}
```

## Setting Up Postman

1. **Create a new collection** in Postman for your API testing.

2. **Add a new request** for each endpoint:
   - Register (POST)
   - Login (POST)
   - Logout (POST)
   - Get User (GET)
   - Notes (GET, POST, GET/{id}, PUT/{id}, DELETE/{id}) - for users with "note" role
   - Products (GET, POST, GET/{id}, PUT/{id}, DELETE/{id}) - for users with "product" role

3. **For the Register request:**
   - Set the method to POST
   - Set the URL to `http://localhost:8000/api/register`
   - Go to the "Body" tab, select "raw" and "JSON"
   - Enter the JSON request body with the new user's details, including the `role` field (either "note" or "product")

4. **For the Login request:**
   - Set the method to POST
   - Set the URL to `http://localhost:8000/api/login`
   - Go to the "Body" tab, select "raw" and "JSON"
   - Enter the JSON request body with your credentials

5. **For authenticated requests (Logout and Get User):**
   - After successful login or registration, copy the `access_token` from the response
   - In your request, go to the "Authorization" tab
   - Select "Bearer Token" from the Type dropdown
   - Paste your token in the Token field

## Testing Flow

1. **Register a user** using the Register endpoint
   - Send a POST request to `/api/register` with the required user details, including the `role` field (either "note" or "product")
   - The role determines which services the user can access (note or product services)
   - You'll receive a JWT token in the response

2. **Login** with the user credentials to get a JWT token
   - This step is optional if you've already registered and received a token

3. **Use the token** for authenticated requests (Logout and Get User)

4. **Test role-based access** to services:
   - If you registered with the "note" role, try accessing the Notes API endpoints
   - If you registered with the "product" role, try accessing the Products API endpoints
   - Try accessing endpoints for the other role to verify that you receive a 403 Forbidden response

5. **Test Logout** to invalidate the token

6. **Verify** that authenticated endpoints no longer work after logout

## Role-Based Access

The application implements role-based access control:

- Users with the **note** role can only access note-related services
- Users with the **product** role can only access product-related services
- The role is specified during registration and cannot be changed later
- Attempting to access services not allowed for your role will result in a 403 Forbidden response

## Notes

- JWT tokens expire after 1 hour (3600 seconds)
- You need to include the token in the Authorization header for authenticated requests
- After logout, the token is invalidated and can no longer be used
