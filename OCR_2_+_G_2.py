import sys, os, json
from PIL import Image
import pytesseract
from pdf2image import convert_from_path
import cv2
import numpy as np
import re
from difflib import SequenceMatcher

def ocr_image(path):
    img = cv2.imread(path, cv2.IMREAD_GRAYSCALE)
    if img is None:
        img = np.array(Image.open(path).convert("L"))

    img = cv2.fastNlMeansDenoising(img, None, 30, 7, 21)
    img = cv2.adaptiveThreshold(img, 255, cv2.ADAPTIVE_THRESH_GAUSSIAN_C, cv2.THRESH_BINARY, 31, 10)

    pil_img = Image.fromarray(img)

    custom_config = (
        r'--oem 3 --psm 6 '
        r'-c tessedit_char_whitelist='
        r'0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'
        r'АБВГДЕЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЬЮЯабвгдежзийклмнопрстуфхцчшщъьюя'
        r'.,:-€§%'
    )

    return pytesseract.image_to_string(pil_img, lang="bul+eng", config=custom_config)

def ocr_pdf(path):
    pages = convert_from_path(path, dpi=300)
    output = []

    for img in pages:
        img_cv = cv2.cvtColor(np.asarray(img), cv2.COLOR_RGB2GRAY)
        img_cv = cv2.fastNlMeansDenoising(img_cv, None, 30, 7, 21)
        img_cv = cv2.adaptiveThreshold(img_cv, 255, cv2.ADAPTIVE_THRESH_GAUSSIAN_C, cv2.THRESH_BINARY, 31, 10)
        pil_img = Image.fromarray(img_cv)

        custom_config = (
            r'--oem 3 --psm 6 '
            r'-c tessedit_char_whitelist='
            r'0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'
            r'АБВГДЕЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЬЮЯабвгдежзийклмнопрстуфхцчшщъьюя'
            r'.,:-€§%'
        )

        output.append(pytesseract.image_to_string(pil_img, lang="bul+eng", config=custom_config))

    return output

def filter_symbols(text):
    text = re.sub(r'(?i)m\s*o', '', text)  
    text = text.replace('№', '')
    text = text.replace("§", "5")
    return re.sub(r'[^0-9A-Za-zА-Яа-я€%,\.\-\s]', '', text)

def separate_keywords(text):
    keywords = ['Номер', 'ДДС №', 'Идент. №', 'Град', 'Адрес', 'МОЛ', 'телефон']
    for kw in keywords:
        text = re.sub(r'(?<!^)(?<!\n)(' + re.escape(kw) + r')', r'\n\1', text)
    return text

def postprocess_text(text):
    for _ in range(3):
        old = text

        text = re.sub(r'HlaHbuHaocHoBa20 ?', 'Данъчна основа 20%', text, flags=re.IGNORECASE)
        text = re.sub(r'ANC20 ?', 'ДДС 20%', text, flags=re.IGNORECASE)
        text = re.sub(r'Cyma3annaljaHe', 'Сума за плащане', text, flags=re.IGNORECASE)

        text = re.sub(r'(\d)\s*[EeЕе](\s|$)', r"\1 €\2", text)

        def cyrillic_pref(m):
            w = m.group(0)
            if any(ch.isdigit() for ch in w):
                return w
            trans = str.maketrans("ABEKMHOPCTXabekmopctx", "АВЕКМНОРСТХавекморстх")
            return w.translate(trans)

        text = re.sub(r"[A-Za-zА-Яа-я]+", cyrillic_pref, text)

        def tax_fix(m):
            key = m.group(1)
            rest = m.group(2)

            match = re.search(r'\d+', rest)
            if match:
                start, end = match.span()
                if end >= len(rest) or rest[end] != '%':
                    rest = rest[:end] + '%' + rest[end:]
            else:
                rest = '20%' + rest 

            return f"{key}{rest}"

        text = re.sub(r'(Данъчна основа|ДДС)([^\n]*)', tax_fix, text, flags=re.IGNORECASE)

        text = re.sub(r'([.,-])([^\s\d])', r'\1 \2', text)
        address_fixes = {
            r'%К30НаВ': 'жк Зона Б',
            r'ТеН\.СтоnетоsNе11': 'бул. Ген. Столетов No11',
            r'кВ\.ВеНКОВСКН,yn\.Ораnuuyа24': 'кв. Бенковски, ул. Оралица 24'
        }
        for k, v in address_fixes.items():
            text = re.sub(k, v, text)

        text = re.sub(r"(\d)([€лв])", r"\1 \2", text)
        text = re.sub(r"\s{2,}", " ", text)

        if text == old:
            break

    return text

def create_shortened_version_from_normal(normal_txt_file_path):
    try:
        with open(normal_txt_file_path, 'r', encoding='utf-8') as f:
            content = f.read()

        key_fields = [
            "Получател", "Доставчик", "ДДС", "Идент. №", "Град", "Адрес", "МОЛ",
            "Данъчна основа", "Данъчна основа 20%", "Сума за плащане", "Дата на данъчно събитие",
            "Плащане", "IBAN", "Банка", "Банков код", "Номер"
        ]

        lines = content.split("\n")
        key_lines = []

        for line in lines:
            best_keyword = None
            norm_line = re.sub(r'\d+', '', line.lower())
            norm_line = re.sub(r'\s+', ' ', norm_line)
            best_ratio = 0

            for field in key_fields:
                norm_field = re.sub(r'\d+', '', field.lower())
                norm_field = re.sub(r'\s+', ' ', norm_field)

                if norm_field in norm_line:
                    best_keyword = field
                    break

                ratio = SequenceMatcher(None, norm_line, norm_field).ratio()
                if ratio > best_ratio and ratio >= 0.8:
                    best_ratio = ratio
                    best_keyword = field

            if not best_keyword:
                continue

            idx = line.lower().find(best_keyword.lower())
            extracted = line[idx:].strip() if idx != -1 else line.strip()
            extracted = re.sub(r"(\d)([€лв])", r"\1 \2", extracted)
            extracted = re.sub(r"\s{2,}", " ", extracted)

            key_lines.append(extracted)

        shortened_path = normal_txt_file_path.replace('_ocr.txt', '_ocr_shortened.txt')
        with open(shortened_path, "w", encoding="utf-8") as f:
            f.write("\n".join(key_lines))

        print(f"Shortened version created: {shortened_path}")

    except Exception as e:
        print(f"Error creating shortened version: {e}")

def main():
    import tkinter as tk
    from tkinter import filedialog
    root = tk.Tk()
    root.withdraw()
    path = filedialog.askopenfilename(
        title="Изберете файл (фактура)", 
        filetypes=[("Images and PDF", "*.jpg *.jpeg *.png *.tif *.tiff *.bmp *.pdf"), ("All files", "*.*")]
    )

    if not path:
        print("Не е избран файл.")
        return

    if not os.path.exists(path):
        print("File not found:", path)
        return

    name = os.path.splitext(os.path.basename(path))[0]
    result = {"file": path, "pages": []}

    if path.lower().endswith((".jpg", ".jpeg", ".png", ".tif", ".tiff", ".bmp")):
        result["pages"] = [ocr_image(path)]
    elif path.lower().endswith(".pdf"):
        result["pages"] = ocr_pdf(path)
    else:
        print("Unsupported file type:", path)
        return

    cleaned = [filter_symbols(p) for p in result["pages"]]
    separated = [separate_keywords(p) for p in cleaned]
    postprocessed = [postprocess_text(p) for p in separated]

    out_txt = f"{name}_ocr.txt"
    with open(out_txt, "w", encoding="utf-8") as f:
        f.write("\n\n".join(postprocessed))

    create_shortened_version_from_normal(out_txt)

if __name__ == "__main__":
    main()
