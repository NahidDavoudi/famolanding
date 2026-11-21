# DESIGN.md — Liquid Glass

## Vibe
Translucent, glossy, futuristic. Frosted glass panels floating over a vivid gradient background with light refraction. Premium and modern. Best for: sneakers, sport and running, tech-feel brands, dark luxury.

## Palette
| Token | Hex | Use |
|---|---|---|
| --bg-from | #0B1020 | gradient start |
| --bg-to | #1B2A5A | gradient end |
| --blob-1 | #7DF9FF | glow blob (cyan) |
| --blob-2 | #B388FF | glow blob (violet) |
| --glass | rgba(255,255,255,0.12) | panel fill |
| --glass-border | rgba(255,255,255,0.28) | panel edge |
| --primary | #7DF9FF | buttons, active states |
| --text | #F5F7FF | text |
| --muted | rgba(245,247,255,0.65) | secondary text |

## Typography
- Persian: Vazirmatn, weights 400 / 600 / 800
- Large light headings over glass, white with subtle text-shadow for legibility
- Prices: 700, slightly larger than name

## Shape and Depth
- Radius: 24px panels, 999px buttons
- Glass recipe:
  `background: rgba(255,255,255,.12); backdrop-filter: blur(24px) saturate(160%); border: 1px solid rgba(255,255,255,.28); box-shadow: 0 8px 32px rgba(0,0,0,.25), inset 0 1px 0 rgba(255,255,255,.4);`
- Background: dark gradient with 2 to 3 blurred color blobs behind the glass (blur 80px)
- Specular highlight: thin white gradient on top edge of panels

## Components
- **Header:** glass bar pinned to top, logo right, cart left
- **Hero:** product photo cutout (shoe or bag) floating above a glowing blob, glass info panel below with headline and "سفارش" button
- **Category chips:** small glass pills, active chip filled with --primary and dark text
- **Product card:** glass card, product image slightly overflowing the top edge, price in a glass tag
- **Order section:** horizontal glass steps with thin glowing connector line

## RTL / Persian Rules
- dir="rtl", mirrored layout
- Keep text contrast at 4.5:1 or higher: add a darker overlay under glass if the blob is bright
- Persian digits for prices

## Performance Notes
- backdrop-filter is heavy on low-end phones: limit to 4 to 6 glass elements per screen
- Provide fallback: solid rgba(20,30,60,.85) when backdrop-filter is unsupported

## Do / Don't
- Do use dark backgrounds, glass barely reads on white
- Do keep blobs behind important content only
- Don't put glass on glass more than 2 layers deep
- Don't use pure black, use deep navy

## Stitch Prompt
```
Mobile-first RTL e-commerce homepage for an Iranian sneaker Instagram shop, liquid glass style.
Dark navy gradient background (#0B1020 to #1B2A5A) with blurred cyan and violet glow blobs,
frosted translucent glass panels with blur, thin white borders and specular top highlight, 24px radius.
Accent #7DF9FF, text #F5F7FF. Font: Vazirmatn.
Sections: glass header, hero with floating sneaker and "سفارش" button, glass category pills,
2-column glass product cards with Toman prices, glass 3-step "how to order", footer with Instagram link.
```
