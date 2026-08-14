import json
from pathlib import Path

# Kunin ang absolute path ng app/data directory
BASE_DIR = Path(__file__).resolve().parent.parent / "data"

def load_customer_data(customer_type: str, file_name: str):
    """"
        Nagbabasa ng Json file base sa category
        customer_type: 'new_customer' or 'active_customer'
        file_name: example: 'customer_data.json'
    """
    customer_folder = BASE_DIR / customer_type

    # Fallback to empty list / default data kung bagong customer at wala pang hiwalay na folder
    if not customer_folder.exists():
        customer_folder = BASE_DIR / "new_customer"

    file_path = customer_folder / file_name

    if not file_path.exists():
        return []  # Return empty list if file does not exist

    with open(file_path, "r", encoding="utf-8") as f:
        return json.load(f)