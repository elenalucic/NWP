const mongoose = require('mongoose');

const ClanSchema = new mongoose.Schema({
  ime: String,
  prezime: String,
  uloga: String
});

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
  clanoviTima: [ClanSchema]
});

module.exports = mongoose.model('Project', ProjectSchema);
