const express = require('express');
const methodOverride = require('method-override');
const path = require('path');
const mongoose = require('mongoose');
const session = require('express-session');
const MongoStore = require('connect-mongo');
const passport = require('./config/passport');
const projectRoutes = require('./routes/projects');
const authRoutes = require('./routes/auth');

const app = express();

// Povezivanje s MongoDB
mongoose.connect('mongodb://localhost/projects_db', {
  useNewUrlParser: true,
  useUnifiedTopology: true
}).then(() => {
  console.log('Connected to MongoDB');
}).catch(err => {
  console.error('MongoDB connection error:', err);
});

// Middleware
app.use(express.urlencoded({ extended: true }));
app.use(methodOverride('_method'));
app.set('view engine', 'ejs');
app.set('views', path.join(__dirname, 'views'));

// Sesije
app.use(session({
  secret: 'your-secret-key',
  resave: false,
  saveUninitialized: false,
  store: MongoStore.create({ mongoUrl: 'mongodb://localhost/projects_db' })
}));

// Inicijalizacija Passport-a
app.use(passport.initialize());
app.use(passport.session());

// Postavljanje trenutnog korisnika za sve predloške
app.use((req, res, next) => {
  res.locals.currentUser = req.user || null; // Sprječava currentUser is not defined
  next();
});

// Rute
app.use('/projects', projectRoutes);
app.use('/auth', authRoutes);

// 404 Handler
app.use((req, res, next) => {
  const err = new Error('Not Found');
  err.status = 404;
  next(err);
});

// Error Handler
app.use((err, req, res, next) => {
  console.error(err.stack);
  res.status(err.status || 500).send(err.message || 'Internal Server Error');
});

module.exports = app; // Eksportira Express app za ./bin/www