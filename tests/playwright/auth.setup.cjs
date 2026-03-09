const { test: setup, expect } = require('@playwright/test');
const path = require('path');
const fs = require('fs');

const BASE_URL = 'http://127.0.0.1:8000';
const AUTH_FILE = 'tests/playwright/.auth/user.json';

setup('Login dan simpan auth state', async ({ page }) => {
  // Pastikan folder ada
  fs.mkdirSync(path.dirname(AUTH_FILE), { recursive: true });

  await page.goto(`${BASE_URL}/login`);
  await page.fill('input[name="email"]', 'admin@aplikatif.com');
  await page.fill('input[name="password"]', 'password');
  await page.click('button[type="submit"]');
  await page.waitForURL(`${BASE_URL}/dashboard`, { timeout: 20000 });
  await expect(page.url()).toContain('/dashboard');

  // Simpan auth state (cookies + localStorage)
  await page.context().storageState({ path: AUTH_FILE });
  console.log('✅ Auth state tersimpan');
});
