# TEXORA V2 – PROJECT RULES

## 🧠 PROJECT OVERVIEW

Texora is a SaaS appointment and client retention system.

Architecture:
- Laravel (monolith)
- Blade (NO React / NO Vue)
- Tailwind CSS
- MySQL
- Single database
- Multi-tenant via `business_id`

---

## 🚨 CORE PRINCIPLES (NON-NEGOTIABLE)

### 1. NO API-FIRST
- No Sanctum
- No API architecture
- No external frontend

### 2. NO FRONTEND FRAMEWORKS
- NO React
- NO Vue
- NO SPA

### 3. MONOLITH ONLY
- Everything inside Laravel app

---

## 🏗️ ARCHITECTURE RULES

### Controllers
- Must be THIN
- NO business logic

### Service Layer (MANDATORY)
All business logic MUST go in services:

- AppointmentService
- ClientService
- NotificationService
- BusinessService

---

### Database Rules
- Every table MUST have `business_id`
- ALWAYS filter queries by `business_id`

---

### Routes
- Protected by auth
- Scoped by business

---

## 🔒 SECURITY RULES

- NEVER access data from another business
- ALWAYS validate ownership
- NO global queries without scope

---

## ⚙️ DEVELOPMENT RULES

### 1. DO NOT BREAK EXISTING STRUCTURE
- Respect current architecture
- Do NOT refactor without reason

### 2. SMALL STEPS ONLY
- Incremental changes
- No big rewrites

### 3. NO MAGIC
- Everything must be explicit and clear

---

## 🚫 FORBIDDEN

- Sanctum
- API tokens
- React / Vue
- WebSockets
- Microservices
- Over-engineering

---

## 🧱 CURRENT STACK (LOCKED)

- Laravel
- Breeze (Blade auth)
- Blade UI
- Tailwind
- MySQL
- Apache

---

## 📦 PROJECT STRUCTURE (IMPORTANT)

- app/Services → business logic
- app/Models → data layer
- app/Http/Controllers → minimal logic
- resources/views → UI
- routes/web.php → routes

---

## 🤖 AI WORKFLOW RULES (STRICT)

Any AI assistant (ChatGPT, Copilot, etc.) MUST follow these rules:

---

### 1. Command Limit
- Provide MAXIMUM 3 commands at a time
- Do NOT overload with multiple steps

---

### 2. Step-by-step execution
- Wait for confirmation after each step
- Do NOT jump ahead

---

### 3. File editing instructions
- ALWAYS use line numbers when modifying code
- Example:
  - Delete line 25–27
  - Insert code at line 29

---

### 4. Clarity
- Commands must be simple and clear
- No unnecessary explanations

---

### 5. Respect existing structure
- Do NOT rewrite entire files
- Only modify what is necessary

---

### 6. Safety
- Do NOT suggest risky or destructive commands without warning

---

## 🚨 FINAL RULE

If AI does NOT follow these rules → IGNORE RESPONSE
