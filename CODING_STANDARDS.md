# Coding Standards for Dooshalah Project

This document outlines the coding standards and naming conventions to be followed throughout the Dooshalah project. Consistency in naming and coding practices ensures readability, maintainability, and scalability of the codebase.

## Naming Conventions

### 1. Classes, Interfaces, Traits, and Enums
- **Convention**: Use `PascalCase` (capitalize the first letter of each word).
- **Purpose**: To clearly identify top-level constructs.
- **Examples**:
  - `Country`
  - `CountryAccessLevel`
  - `UserProfile`

### 2. Methods and Functions
- **Convention**: Use `camelCase` (first word lowercase, subsequent words capitalized).
- **Purpose**: To distinguish methods from classes and improve readability.
- **Examples**:
  - `getCachedCountry`
  - `isFree`
  - `calculateTotal`

### 3. Variables and Properties
- **Convention**: Use `camelCase` for variables, except for database columns which use `snake_case`.
- **Purpose**: To differentiate between temporary variables and persistent data.
- **Examples**:
  - Variables: `$userCount`, `$isActive`
  - Database Columns: `first_name`, `access_level`

### 4. Database Columns
- **Convention**: Use `snake_case` (words separated by underscores).
- **Purpose**: To align with Laravel's database conventions.
- **Examples**:
  - `name`
  - `flag_image`
  - `born_country`

### 5. Scopes (Eloquent Query Scopes)
- **Convention**: Start with `scope` followed by `PascalCase`.
- **Purpose**: To follow Laravel's convention for query scopes.
- **Examples**:
  - `scopeFree`
  - `scopeRegistrationRequired`

### 6. Enum Values
- **Convention**: Use `PascalCase` for enum case names and `snake_case` for their string values.
- **Purpose**: To differentiate between the enum identifier and its stored value.
- **Examples**:
  - `Free = 'free'`
  - `RegistrationRequired = 'registration_required'`

### 7. Relationships (Eloquent Relations)
- **Convention**: Use `camelCase`, pluralize for "has many" relationships.
- **Purpose**: To align with Laravel's relationship naming conventions.
- **Examples**:
  - `bornUsers` (HasMany)
  - `livingCountry` (BelongsTo)

### 8. File Names
- **Convention**: Match the file name to the class name using `PascalCase`.
- **Purpose**: To ensure consistency between file and class names.
- **Examples**:
  - `Country.php`
  - `CountryAccessLevel.php`

## Code Documentation
- **PHPDoc**: All classes, methods, and properties must include PHPDoc comments.
- **Details**: Include `@param`, `@return`, `@property`, and other relevant tags as needed.
- **Example**:
  ```php
  /**
   * Get the cached country details for performance.
   *
   * @return Country
   */
  public function getCachedCountry(): Country
  {
      return Cache::remember("country_{$this->id}", 3600, fn () => $this);
  }
