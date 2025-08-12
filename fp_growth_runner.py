#!/usr/bin/env python3
"""
Runner sederhana untuk analisis FP-Growth.
Cara panggil dari PHP (Process):
  python fp_growth_runner.py input.json output.json

Format input.json:
{
  "transactions": {
    "INV-001": ["Produk A", "Produk B"],
    "INV-002": ["Produk A", "Produk C"]
  }
}

Format output.json (contoh):
[
  {"antecedent_ids": [1], "consequent_ids": [2,3], "support": 0.2, "confidence": 0.6, "lift": 1.2}
]
"""
import json
import sys
from pathlib import Path

# Instalasi: pip install mlxtend
try:
    from mlxtend.frequent_patterns import fpgrowth, association_rules
    import pandas as pd
except Exception as e:
    print("Library mlxtend/pandas belum terpasang. Jalankan: pip install mlxtend pandas", file=sys.stderr)
    raise


def main():
    if len(sys.argv) < 3:
        print("Usage: fp_growth_runner.py <input.json> <output.json>", file=sys.stderr)
        sys.exit(1)

    in_path = Path(sys.argv[1])
    out_path = Path(sys.argv[2])

    with in_path.open('r', encoding='utf-8') as f:
        data = json.load(f)

    # transactions: dict invoice -> list of product_ids
    transactions = data.get('transactions', {})

    # Flatten product ids
    baskets = list(transactions.values())
    if not baskets:
        with out_path.open('w', encoding='utf-8') as f:
            json.dump([], f)
        return

    # Build one-hot encoded dataframe for FP-Growth
    # Kumpulkan semua product_id unik
    all_pids = sorted({pid for basket in baskets for pid in basket})
    # Buat records boolean per basket
    records = []
    for basket in baskets:
        present = set(basket)
        row = {pid: (pid in present) for pid in all_pids}
        records.append(row)
    df = pd.DataFrame(records)

    # Frequent itemsets
    freq = fpgrowth(df, min_support=0.02, use_colnames=True)  # min_support bisa disesuaikan
    if freq.empty:
        with out_path.open('w', encoding='utf-8') as f:
            json.dump([], f)
        return

    # Association rules
    rules_df = association_rules(freq, metric='confidence', min_threshold=0.3)
    # Konversi ke format yang diharapkan Laravel
    rules = []
    for _, r in rules_df.iterrows():
        ant = sorted(list(r['antecedents']))
        cons = sorted(list(r['consequents']))
        if not ant or not cons:
            continue
        rules.append({
            'antecedent_ids': [int(x) for x in ant],
            'consequent_ids': [int(x) for x in cons],
            'support': float(r.get('support', 0.0)),
            'confidence': float(r.get('confidence', 0.0)),
            'lift': float(r.get('lift', 0.0)),
        })

    with out_path.open('w', encoding='utf-8') as f:
        json.dump(rules, f)


if __name__ == '__main__':
    main()

