<template>
  <section class="chessboard_wrapper">
    <div class="chessboard">
      <ul class="chessboard__row" v-for="(row, rowIndex) in boardState" :key="rowIndex">
        <li
          class="chessboard__tile"
          v-for="(column, columnIndex) in row"
          :key="columnIndex"
          :class="{ active: column === 1, disabled: column === -1 }"
          @click="$emit('tile-click', rowIndex, columnIndex)"
        ></li>
      </ul>
    </div>
  </section>
</template>

<script lang="ts">
import Vue from 'vue';

export default Vue.extend({
  props: {
    boardState: {
      type: [Array],
      required: true,
    },
  },
  data() {
    return {};
  },
});
</script>

<style scoped lang="scss">
$chessboard-wrapper-background-color: #fafafa;
$chessboard-background-color: #424242; // color between the tiles

$chessboard-tile-background-color--light: #f4f4f4;
$chessboard-tile-background-color--dark: #303030;
$chessboard-tile-background-color--active: #1967c0;
$chessboard-tile-background-color--disabled: #f44236;
$chessboard-tile-size: 40px;

.chessboard_wrapper {
  border-radius: 8px;
  box-shadow: 0px 7px 8px -4px rgba(0, 0, 0, 0.2),
    0px 12px 17px 2px rgba(0, 0, 0, 0.14), 0px 5px 22px 4px rgba(0, 0, 0, 0.12);
  padding: 4px;
  background-color: $chessboard-wrapper-background-color;
}
.chessboard {
  background-color: $chessboard-background-color;
}
ul.chessboard__row {
  display: flex;
  margin: 0px;
  padding: 0px;
  list-style-type: none;
}
.chessboard__tile {
  display: flex;
  justify-content: center;
  align-items: center;

  width: $chessboard-tile-size;
  height: $chessboard-tile-size;
  border: 1px solid transparent;

  &:hover {
    cursor: pointer;
    border: 1px solid $chessboard-tile-background-color--active;
  }
  &.disabled {
    cursor: not-allowed;
    border: 1px solid transparent;

    &::after {
      display: block;
      content: " ";
      width: $chessboard-tile-size;
      height: $chessboard-tile-size;
      background-color: $chessboard-tile-background-color--disabled;
      opacity: 0.2;
    }
  }
  &.active::after {
    display: block;
    content: " ";
    width: $chessboard-tile-size - 4px;
    height: $chessboard-tile-size - 4px;
    background-color: $chessboard-tile-background-color--active;
    opacity: 0.8;
    border-radius: 100%;
  }
}
.chessboard__row:nth-child(even) .chessboard__tile:nth-child(even),
.chessboard__row:nth-child(odd) .chessboard__tile:nth-child(odd) {
  background-color: $chessboard-tile-background-color--dark;
}
.chessboard__row:nth-child(odd) .chessboard__tile:nth-child(even),
.chessboard__row:nth-child(even) .chessboard__tile:nth-child(odd) {
  background-color: $chessboard-tile-background-color--light;
}
</style>
