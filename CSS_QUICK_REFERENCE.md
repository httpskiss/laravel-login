# CSS Files Quick Reference Guide

## File Organization by Function

### Core Styles
| File | Purpose | Key Classes |
|------|---------|-------------|
| `app.css` | Main entry point, imports all CSS modules | - |

### Layout & Navigation
| File | Purpose | Key Classes |
|------|---------|-------------|
| `layout-sidebar.css` | Sidebar, navigation, responsive layout | `.sidebar`, `.nav-item`, `.collapse-btn`, `.sidebar-overlay` |

### Feature-Specific Styles
| File | Purpose | Key Classes |
|------|---------|-------------|
| `attendance.css` | Attendance tracking, QR scanner | `.attendance-card`, `.qr-scanner`, `.status-*` |
| `payroll.css` | Payroll information display | `.leave-card`, `.status-badge`, `.type-*`, `.balance-card` |
| `dashboard.css` | Dashboard animations & effects | `.animate-fade-in`, `.gradient-bg`, `.glow-on-hover`, `.pulse` |
| `auth.css` | Login & authentication forms | `.form-flip`, `.input-field`, `.btn-primary`, `.social-btn` |
| `calendar.css` | Calendar component display | `.calendar-day`, `.present`, `.absent`, `.legend-dot` |
| `leaves.css` | Leave application forms | `.form-section`, `.floating-label`, `.progress-bar`, `.conditional-section` |

---

## Animation Classes Quick Reference

### Fade-In Animations
```css
.animate-fade-in              /* Dashboard, leaves, payroll */
```

### Hover Effects
```css
.quick-action-card:hover      /* Dashboard cards */
.leave-card:hover             /* Payroll cards */
.calendar-day:hover           /* Calendar dates */
.glow-on-hover:hover          /* Dashboard icons */
```

### Status Indicators
```css
.status-present               /* Green - attendance */
.status-late                  /* Orange - attendance */
.status-absent                /* Red - attendance */
.status-leave                 /* Blue - attendance */

.status-approved              /* Leave badge */
.status-pending               /* Leave badge */
.status-rejected              /* Leave badge */
.status-cancelled             /* Leave badge */
```

### Leave Types
```css
.type-vacation                /* Blue */
.type-sick                    /* Purple */
.type-emergency               /* Orange */
.type-maternity               /* Pink */
.type-paternity               /* Cyan */
.type-conference              /* Light blue */
.type-research                /* Light green */
.type-sabbatical              /* Gray */
```

---

## Responsive Design Breakpoints

Most stylesheets include responsive media queries:
- **Mobile**: `max-width: 768px`
- **Adjustments**: Grid layouts, flexbox stacking, full-width elements

---

## Adding New Styles

### Step 1: Create New CSS File
```bash
# Example: resources/css/new-feature.css
touch resources/css/new-feature.css
```

### Step 2: Add Import to app.css
```css
@import './new-feature.css';
```

### Step 3: Remove Inline Styles
Remove `<style>` blocks from your Blade files and use class-based styling.

### Step 4: Build with Vite
```bash
npm run dev      # Development
npm run build    # Production
```

---

## Common CSS Classes & Transitions

### Transitions Used
- `transition: all 0.3s ease` - General smooth transitions
- `transition: all 0.2s ease` - Faster interactions
- `transition: transform 0.3s ease` - Transform animations

### Z-Index Stack
- `z-0` - Background
- `z-10` - Cards, modals
- `z-40` - Sidebar
- `z-45` - Overlay
- `z-50` - Mobile menu, fixed elements

### Border Radius
- Small: `0.375rem` (6px)
- Medium: `0.5rem` (8px)
- Large: `9999px` (Pills, circles)

---

## Color Scheme Reference

### Primary Blue (Sidebar)
```css
#1e3a8a      /* Dark blue (sidebar base) */
#3b82f6      /* Bright blue (accent) */
#dbeafe      /* Light blue (background) */
```

### Status Colors
```css
#10b981      /* Green (present) */
#f59e0b      /* Orange (late) */
#ef4444      /* Red (absent) */
#7c3aed      /* Purple (leave) */
#059669      /* Dark green (holiday) */
```

### Neutral Palette
```css
#f3f4f6      /* Light gray (bg) */
#9ca3af      /* Medium gray (text) */
#4b5563      /* Dark gray (text) */
```

---

## Debugging Tips

1. **Check if CSS is imported**: Open browser DevTools → Sources tab → look for CSS files
2. **Check specificity**: CSS classes should have equal specificity
3. **Verify Vite compilation**: Run `npm run dev` and check console for errors
4. **Hot module replacement**: Changes to CSS files should auto-reload in development
5. **Cache busting**: Hard refresh (Ctrl+Shift+R / Cmd+Shift+R) if styles don't update

---

## Performance Tips

✅ CSS is minified in production builds  
✅ Vite bundles all CSS into optimized chunks  
✅ Source maps available in development for debugging  
✅ Unused CSS is treeshaken with Tailwind integration  

---

## Module-by-Module Checklist

- [x] Sidebar & Navigation - `layout-sidebar.css`
- [x] Attendance System - `attendance.css`
- [x] Payroll Pages - `payroll.css`
- [x] Dashboard - `dashboard.css`
- [x] Authentication - `auth.css`
- [x] Calendar - `calendar.css`
- [x] Leave Forms - `leaves.css`
- [x] Main imports - `app.css`

---

**Last Updated**: November 14, 2025  
**Framework**: Laravel 11 + Vite + Tailwind CSS
