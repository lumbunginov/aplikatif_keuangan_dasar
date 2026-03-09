const { test, expect } = require('@playwright/test');

const BASE_URL = 'http://127.0.0.1:8000';

// Semua test di sini sudah authenticated via storageState (auth.setup.cjs)
// Tidak perlu login ulang setiap test — hemat rate limit!

test.describe('Aplikatif Base Template', () => {

  test('1. Dashboard tampil dengan konten', async ({ page }) => {
    await page.goto(`${BASE_URL}/dashboard`);
    await expect(page).not.toHaveURL(/login/);
    const bodyText = await page.textContent('body');
    expect(/pagi|siang|sore|malam|selamat|dashboard|user|total|activity/i.test(bodyText)).toBeTruthy();
    console.log('✅ Dashboard tampil OK');
  });

  test('2. Sidebar navigation tampil', async ({ page }) => {
    await page.goto(`${BASE_URL}/dashboard`);
    const bodyText = await page.textContent('body');
    expect(/user|dashboard|setting|role|activity|log/i.test(bodyText)).toBeTruthy();
    console.log('✅ Navigasi sidebar tampil');
  });

  test('3. Halaman User Management dapat diakses', async ({ page }) => {
    await page.goto(`${BASE_URL}/users`);
    await page.waitForLoadState('networkidle');
    await expect(page).not.toHaveURL(/login/);
    const bodyText = await page.textContent('body');
    expect(/user|nama|email/i.test(bodyText)).toBeTruthy();
    console.log('✅ Halaman Users OK');
  });

  test('4. Halaman Roles dapat diakses', async ({ page }) => {
    await page.goto(`${BASE_URL}/roles`);
    await page.waitForLoadState('networkidle');
    await expect(page).not.toHaveURL(/login/);
    const bodyText = await page.textContent('body');
    expect(/role|superadmin|admin|permission/i.test(bodyText)).toBeTruthy();
    console.log('✅ Halaman Roles OK');
  });

  test('5. Halaman Settings dapat diakses (superadmin)', async ({ page }) => {
    await page.goto(`${BASE_URL}/settings`);
    await page.waitForLoadState('networkidle');
    await expect(page).not.toHaveURL(/login/);
    const bodyText = await page.textContent('body');
    expect(/setting|aplikasi|app|nama|logo/i.test(bodyText)).toBeTruthy();
    console.log('✅ Halaman Settings OK');
  });

  test('6. Halaman Activity Log dapat diakses', async ({ page }) => {
    await page.goto(`${BASE_URL}/activity-log`);
    await page.waitForLoadState('networkidle');
    await expect(page).not.toHaveURL(/login/);
    console.log('✅ Halaman Activity Log OK');
  });

  test('7. Protected route redirect ke login jika belum auth', async ({ browser }) => {
    // Buka context baru tanpa auth state
    const context = await browser.newContext();
    const page = await context.newPage();
    await page.goto(`${BASE_URL}/dashboard`);
    await expect(page).toHaveURL(/login/);
    await context.close();
    console.log('✅ Route protected bekerja → redirect ke login');
  });

  test('8. Login gagal menampilkan pesan error', async ({ browser }) => {
    // Buka context baru tanpa auth
    const context = await browser.newContext();
    const page = await context.newPage();
    await page.goto(`${BASE_URL}/login`);
    await page.fill('input[name="email"]', 'admin@aplikatif.com');
    await page.fill('input[name="password"]', 'wrongpassword123');
    await page.click('button[type="submit"]');
    await page.waitForTimeout(2000);
    expect(page.url()).toContain('/login');
    const bodyText = await page.textContent('body');
    const hasError = /salah|invalid|wrong|incorrect|credentials|gagal|tidak valid/i.test(bodyText);
    expect(hasError).toBeTruthy();
    await context.close();
    console.log('✅ Login gagal → error tampil');
  });

  test('9. manifest.json valid (PWA)', async ({ page }) => {
    const response = await page.goto(`${BASE_URL}/manifest.json`);
    expect(response.status()).toBe(200);
    const json = JSON.parse(await response.text());
    expect(json).toHaveProperty('name');
    expect(json).toHaveProperty('icons');
    expect(Array.isArray(json.icons)).toBeTruthy();
    console.log(`✅ PWA manifest valid — name: "${json.name}", icons: ${json.icons.length}`);
  });

  test('10. Service Worker (sw.js) tersedia', async ({ page }) => {
    const response = await page.goto(`${BASE_URL}/sw.js`);
    expect(response.status()).toBe(200);
    const content = await response.text();
    expect(content.length).toBeGreaterThan(10);
    console.log('✅ sw.js tersedia & ada konten');
  });

  test('11. Offline page tersedia', async ({ page }) => {
    const response = await page.goto(`${BASE_URL}/offline.html`);
    expect(response.status()).toBe(200);
    console.log('✅ offline.html tersedia');
  });

  test('12. Logout berfungsi', async ({ page }) => {
    await page.goto(`${BASE_URL}/dashboard`);
    await expect(page).not.toHaveURL(/login/);

    const logoutBtn = page.locator([
      'form[action*="logout"] button',
      'button:has-text("Logout")',
      'a:has-text("Logout")',
      'button:has-text("Keluar")',
      'a:has-text("Keluar")',
      'button:has-text("Sign Out")',
    ].join(', ')).first();

    if (await logoutBtn.isVisible({ timeout: 3000 }).catch(() => false)) {
      await logoutBtn.click();
    } else {
      const token = await page.locator('meta[name="csrf-token"]').getAttribute('content');
      await page.evaluate(async (csrf) => {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/logout';
        const input = document.createElement('input');
        input.type = 'hidden'; input.name = '_token'; input.value = csrf;
        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
      }, token);
    }
    await page.waitForTimeout(2000);
    expect(page.url()).toContain('/login');
    console.log('✅ Logout OK → redirect ke login');
  });

});
