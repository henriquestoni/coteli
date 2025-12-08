#!/usr/bin/env bash
# Gera um zip do estado atual do repositório para compartilhamento rápido.
# Uso: scripts/export_latest.sh [destino]
set -euo pipefail

DEST=${1:-dist}
mkdir -p "$DEST"

STAMP=$(date +%Y%m%d-%H%M%S)
ARCHIVE="$DEST/coteli-${STAMP}.zip"

git archive --format=zip HEAD -o "$ARCHIVE"

echo "Arquivo gerado: $ARCHIVE"
