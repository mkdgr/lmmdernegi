#!/usr/bin/env python3
"""Prototip sayfalarını ortak parçalarla (header/footer/ikonlar) birleştirir.

Kullanım:  python3 tasarim/build.py
Kaynak:    tasarim/_src/pages/**/*.html  (üstte --- ile ayrılmış başlık bilgisi)
Çıktı:     tasarim/**/*.html

Laravel'e geçişte her parça bir Blade bileşenine dönüşecek:
  partials/header.*.html -> resources/views/partials/header.blade.php
  {{cur:x}}               -> @if($active === 'x') aria-current="page" @endif
"""
import re
from pathlib import Path

ROOT = Path(__file__).resolve().parent
SRC = ROOT / "_src"
NAV_KEYS = ["home", "hastalik", "rehber", "destek", "etkinlik", "hikaye", "kurumsal"]

LAYOUT = """<!doctype html>
<html lang="{lang}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{title}</title>
<meta name="description" content="{description}">
<meta name="theme-color" content="#005495">
<link rel="icon" href="{base}assets/img/logo.png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,600;12..96,700;12..96,800&family=Figtree:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{base}assets/css/main.css">
</head>
<body>
{icons}
{header}
{content}
{footer}
<script src="{base}assets/js/main.js"></script>
</body>
</html>
"""


def parse(text):
    m = re.match(r"---\n(.*?)\n---\n", text, re.S)
    meta = dict(line.split(": ", 1) for line in m.group(1).splitlines())
    return meta, text[m.end():]


def render(tpl, base, active):
    for key in NAV_KEYS:
        tpl = tpl.replace("{{cur:%s}}" % key, ' aria-current="page"' if key == active else "")
    return tpl.replace("{{base}}", base)


def main():
    icons = (SRC / "partials/icons.html").read_text()
    for page in sorted((SRC / "pages").rglob("*.html")):
        rel = page.relative_to(SRC / "pages")
        base = "../" * (len(rel.parts) - 1)
        meta, content = parse(page.read_text())
        lang = meta["lang"]
        html = LAYOUT.format(
            lang=lang,
            title=meta["title"],
            description=meta["description"],
            base=base,
            icons=icons.strip(),
            header=render((SRC / f"partials/header.{lang}.html").read_text(), base, meta["active"]).strip(),
            content=content.strip(),
            footer=render((SRC / f"partials/footer.{lang}.html").read_text(), base, meta["active"]).strip(),
        )
        out = ROOT / rel
        out.parent.mkdir(parents=True, exist_ok=True)
        out.write_text(html)
        print("✓", out.relative_to(ROOT.parent))


if __name__ == "__main__":
    main()
