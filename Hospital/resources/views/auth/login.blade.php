<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Connexion Clinique</title>
  <!-- Lien vers Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #e8f5e9; /* vert très clair */
    }
    .login-card {
      max-width: 400px;
      margin: 50px auto;
      padding: 30px;
      border-radius: 10px;
      background-color: #ffffff;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .btn-green {
      background-color: #2e7d32;
      color: #fff;
    }
    .btn-green:hover {
      background-color: #1b5e20;
      color: #fff;
    }
    .logo-zone {
      text-align: center;
      margin-bottom: 20px;
    }
    .logo-zone img {
      max-width: 120px;
    }
  </style>
</head>
<body>

  <div class="login-card">
    <!-- Zone du logo -->
    <div class="logo-zone">
      <!-- Remplacez src par le logo de votre clinique -->
      <img src="logo.png" alt="Logo Clinique">
    </div>

    <h3 class="text-center text-success mb-4">Connexion</h3>

    <form action="{{route('login.submit')}}" method="POST">
        @csrf
      <div class="mb-3">
        <label for="email" class="form-label">Adresse Email</label>
        <input type="email" class="form-control" name="email" id="email" placeholder="exemple@gmail.com" required>
        @error('email')
            <div class="invalid-feedback">{{$message}}</div>
        @enderror
      </div>

      <div class="mb-3">
        <label for="password" class="form-label">Mot de passe</label>
        <input type="password" 
           name="password" 
           id="password" 
           class="form-control @error('password') is-invalid @enderror" 
           placeholder="********" 
           required>
        @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>


      <div class="d-grid mb-3">
        <button type="submit" class="btn btn-green">Se connecter</button>
      </div>

      <div class="text-center">
        <a href="{{route('inscription')}}" class="text-success">S’inscrire</a>
      </div>
    </form>
  </div>

  <!-- Script Bootstrap -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
