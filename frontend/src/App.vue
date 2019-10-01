<template>
  <div id="app">
    <TheHeader />

    <main>
      <ChessboardManager
        :settings="settings"
        :results="results"
        :resultsLoading="resultsLoading"
        :resultSolutionPreviewIndex="resultSolutionPreviewIndex"
        @calculate-click="calculateClickHandler"
        @reset-click="resetClickHandler"
        @preview-solution-change="previewSolutionChangeHandler"
      />

      <Chessboard @tile-click="tileClickHandler" :board-state="boardState" />
    </main>

    <TheFooter />
  </div>
</template>

<script lang="ts">
import Vue from 'vue';
import axios from 'axios';
import TheFooter from './components/TheFooter.vue';
import TheHeader from './components/TheHeader.vue';
import Chessboard from './components/Chessboard.vue';
import ChessboardManager from './components/ChessboardManager.vue';

export default Vue.extend({
  name: 'app',

  data() {
    return {
      boardState: [] as number[][] /* -1: disabled, 0: available, 1: active */,

      settings: {
        boardSize: 5, /* rows and columns */
      },

      results: null,
      resultSolutionPreviewIndex: -1 /* the solution displayed on the board */,
      resultsLoading: false,
    };
  },
  beforeMount() {
    this.initializeBoard();
  },
  methods: {
    tileClickHandler(rowIndex: number, columnIndex: number) {
      const row: number[] = this.boardState[rowIndex];
      const currentValue: number = row[columnIndex];
      if (currentValue === -1) {
        return;
      }

      row[columnIndex] = 1 - currentValue;
      /* overwrite entire row (vue-reactivity) */
      this.$set(this.boardState, rowIndex, row);
    },
    calculateClickHandler() {
      this.resultsLoading = true;
      this.resultSolutionPreviewIndex = -1;
      this.results = null;
      axios
        .post('https://calm-tor-50137.herokuapp.com/queensproblemsolver', {
          board: this.boardState,
        })
        .then((response) => {
          console.log(response.data);
          this.results = response.data;
          this.resultsLoading = false;
        });
    },
    resetClickHandler() {
      this.results = null;
      this.initializeBoard();
    },
    previewSolutionChangeHandler(index: number) {
      this.resultSolutionPreviewIndex = index;

      try {
        // @ts-ignore null
        this.boardState = this.results.solutions[index];
      } catch (e) {
        console.log(e);
      }
    },
    initializeBoard() {
      /* creates a 2 dimensional array filled with 0s */
      this.boardState = Array(this.settings.boardSize).fill(null) as number[][];
      this.boardState.forEach((_, rowIndex) => {
        const row = Array(this.settings.boardSize).fill(0) as number[];
        this.$set(this.boardState, rowIndex, row);
      });
    },
  },
  watch: {
    settings: {
      handler() {
        this.initializeBoard();
      },
      deep: true,
    },
  },
  components: {
    TheHeader,
    TheFooter,
    Chessboard,
    ChessboardManager,
  },
});
</script>

<style lang="scss">
$background-color: #fafafa;
$accent-color: #2c3e4f;
$color: black;

html {
  background-color: $background-color;
}
body {
  margin: 0px;
}
#app {
  font-family: "Avenir", Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  color: #2c3e50;

  display: flex;
  flex-direction: column;
  justify-content: space-between;

  min-height: 100vh;
}
main {
  display: flex;
  justify-content: center;
  align-items: center;

  @media (max-width: 600px) {
    flex-direction: column;
  }
}
</style>
