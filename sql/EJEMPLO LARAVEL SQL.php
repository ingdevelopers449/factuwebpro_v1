Schema::create('usuarios', function (Blueprint $table) {
    // PK autoincremental
    $table->id('id_usuario');

    // FK obligatoria hacia empresa
    $table->foreignId('id_empresa')
        ->constrained('empresa', 'id_empresa')
        ->restrictOnDelete();

    // VARCHAR(100) NOT NULL → sin ->nullable()
    $table->string('nombre', 100);

    // VARCHAR(150) NOT NULL UNIQUE
    $table->string('email', 150)->unique();

    // VARCHAR(255) NOT NULL
    $table->string('password_hash', 255);

    // FK opcional (el SQL no dice NOT NULL) → nullable()
    $table->foreignId('id_rol')
        ->nullable()
        ->constrained('roles', 'id_rol')
        ->restrictOnDelete();

    // ENUM con valor por defecto
    $table->enum('estado', ['activo', 'bloqueado', 'inactivo'])->default('activo');

    // INT con default
    $table->integer('intentos_fallidos')->default(0);

    // DATETIME opcional (no tiene NOT NULL)
    $table->dateTime('ultimo_acceso')->nullable();

    // VARCHAR NULL explícito
    $table->string('token_recuperacion', 255)->nullable();

    // DECIMAL(5,2) con default
    $table->decimal('porcentaje_comision', 5, 2)->default(0.00);
});