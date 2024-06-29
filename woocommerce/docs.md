# API and Feature Logic Documentation

## API Documentation

### 1. Products API

#### 1.1 Get All Products
- **Endpoint:** `GET /api/v1/products`
- **Description:** Retrieves all products from WooCommerce and syncs them with the local MongoDB database.
- **Response:** Array of product objects
  ```json
  [
    {
      "product_id": 123,
      "sku": "PROD-001",
      "title": "Sample Product",
      "price": 19.99,
      "quantity": 100,
      "image_url": "https://example.com/image.jpg",
      "shipping_price": "flat_rate",
      "category": "Electronics",
      "link": "https://example.com/product",
      "cost": 10.00,
      "margin": 9.99,
      "margin_percentage": 99.9,
      "units_sold": 50
    }
  ]
  ```

#### 1.2 Get Single Product
- **Endpoint:** `GET /api/v1/products/{id}`
- **Description:** Retrieves a single product by its ID.
- **Parameters:**
  - `id` (path parameter): The ID of the product
- **Response:** Single product object (see structure above)

#### 1.3 Create Product
- **Endpoint:** `POST /api/v1/products`
- **Description:** Creates a new product in WooCommerce and syncs it with the local database.
- **Request Body:**
  ```json
  {
    "name": "New Product",
    "type": "simple",
    "regular_price": "29.99",
    "description": "Product description",
    "short_description": "Short description",
    "categories": [{"id": 1}],
    "images": [{"src": "https://example.com/image.jpg"}]
  }
  ```
- **Response:** Created product object

#### 1.4 Update Product
- **Endpoint:** `PUT /api/v1/products/{id}`
- **Description:** Updates an existing product in WooCommerce and syncs it with the local database.
- **Parameters:**
  - `id` (path parameter): The ID of the product to update
- **Request Body:** Same as Create Product, with fields to update
- **Response:** Updated product object

#### 1.5 Update Product Cost
- **Endpoint:** `PATCH /api/v1/products/{id}/cost`
- **Description:** Updates the cost of a product and recalculates margin and margin percentage.
- **Parameters:**
  - `id` (path parameter): The ID of the product to update
- **Request Body:**
  ```json
  {
    "cost": 15.00
  }
  ```
- **Response:** Updated product object

### 2. Orders API

#### 2.1 Get Order Analytics
- **Endpoint:** `GET /api/v1/orders/analytics`
- **Description:** Retrieves order analytics for a specified date range.
- **Query Parameters:**
  - `start_date` (required): Start date for analytics (format: YYYY-MM-DD)
  - `end_date` (required): End date for analytics (format: YYYY-MM-DD)
- **Response:**
  ```json
  {
    "order_count": 100,
    "revenue": 5000.00,
    "margin": 2000.00
  }
  ```

### 3. Customers API

#### 3.1 Get All Customers
- **Endpoint:** `GET /api/v1/customers`
- **Description:** Retrieves all customers from WooCommerce and syncs them with the local database.
- **Response:** Array of customer objects
  ```json
  [
    {
      "customer_id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "age": 30,
      "city": "New York",
      "order_count": 5,
      "ltv": 500.00
    }
  ]
  ```

#### 3.2 Update Customer LTV
- **Endpoint:** `PATCH /api/v1/customers/{id}/ltv`
- **Description:** Updates a customer's Lifetime Value (LTV) and order count.
- **Parameters:**
  - `id` (path parameter): The ID of the customer to update
- **Response:** Updated customer object

## Feature Logic Documentation

### 1. Product Management

#### 1.1 Product Synchronization
- The API synchronizes products between WooCommerce and the local MongoDB database.
- When retrieving all products, the API fetches products from WooCommerce in batches of 100.
- For each product, the API maps WooCommerce data to the local database schema.
- The API calculates and stores additional fields such as margin and margin percentage.

#### 1.2 Product Cost Update
- The API allows updating the cost of a product, which triggers a recalculation of margin and margin percentage.
- The updated product information is stored in the local database but not synced back to WooCommerce.

#### 1.3 Asynchronous Product Updates
- The API uses a job queue (UpdateMongoDBProduct job) to handle product updates asynchronously.
- This ensures that large numbers of product updates don't block the main application thread.

### 2. Order Analytics

#### 2.1 Date Range Analytics
- The API calculates order analytics for a specified date range.
- It retrieves completed orders from WooCommerce within the given date range.
- The API calculates total order count, revenue, and margin for the period.

#### 2.2 Margin Calculation
- For each order, the API calculates the margin by subtracting the product cost from the revenue.
- Product costs are retrieved from the local database, as this information is not stored in WooCommerce.

#### 2.3 Product Sales Tracking
- When processing orders, the API updates the `units_sold` field for each product in the local database.

### 3. Customer Management

#### 3.1 Customer Synchronization
- The API synchronizes customer data between WooCommerce and the local MongoDB database.
- When retrieving all customers, the API fetches customers from WooCommerce in batches of 100.
- For each customer, the API maps WooCommerce data to the local database schema.

#### 3.2 Lifetime Value (LTV) Calculation
- The API calculates a customer's LTV based on the total margin of all their orders.
- This calculation is performed on-demand when updating a customer's LTV.

#### 3.3 Order Count Tracking
- The API maintains a count of orders for each customer in the local database.
- This count is updated when calculating the customer's LTV.

### 4. Data Security and Performance

#### 4.1 Input Validation
- The API implements input validation for all endpoints to ensure data integrity and prevent injection attacks.

#### 4.2 Pagination
- The API implements pagination when fetching large datasets (products and customers) to manage memory usage and improve response times.

#### 4.3 Asynchronous Processing
- The API uses job queues for time-consuming tasks to improve responsiveness and scalability.

#### 4.4 Database Indexing
- The MongoDB collections are assumed to have appropriate indexing for fields frequently used in queries (e.g., `product_id`, `customer_id`) to optimize query performance.

### 5. Error Handling

#### 5.1 Exception Handling
- The API implements try-catch blocks to handle exceptions gracefully.
- In case of errors, appropriate HTTP status codes and error messages are returned to the client.

### 6. Extensibility

#### 6.1 Repository Pattern
- The API uses the repository pattern, allowing for easy switching between different data sources if needed in the future.

#### 6.2 Service Layer
- Business logic is encapsulated in service classes, promoting separation of concerns and making the codebase easier to maintain and extend.