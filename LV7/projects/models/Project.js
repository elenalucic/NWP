const mongoose = require('mongoose');

const TaskSchema = new mongoose.Schema({
  description: String,
  completed: {
    type: Boolean,
    default: false
  },
  completedAt: Date
});

const ProjectSchema = new mongoose.Schema({
  naziv: String,
  opis: String,
  cijena: Number,
  tasks: {
    type: [TaskSchema],
    default: []
  },
  datumPocetka: Date,
  datumZavrsetka: Date,
  voditelj: {
    type: mongoose.Schema.Types.ObjectId,
    ref: 'User'
  },
  clanoviTima: [{
    type: mongoose.Schema.Types.ObjectId,
    ref: 'User'
  }],
  arhiviran: {
    type: Boolean,
    default: false
  }
});

module.exports = mongoose.model('Project', ProjectSchema);