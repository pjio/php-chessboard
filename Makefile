phar:
	@[ -f build/chess.phar ] && rm build/chess.phar || true
	docker-compose -f docker-compose.phar-builder.yml run --rm phar-builder
	@[ -f build/chess.phar ] && ls -l build/chess.phar || echo "make phar failed!"
