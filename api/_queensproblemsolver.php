<?php

class QueensProblem {
    /**
     * A tile can have 3 values, that are tightly coupled with the front-end
     */
    public const TILE_QUEEN_VALUE = 1;
    public const TILE_EMPTY_VALUE = 0;
    public const TILE_BLOCKED_VALUE = -1;

    /**
     * Computes all solutions for the given board
     */
    public static function solve($board) {
        $isValidBoard = QueensProblem::isValidBoard($board);
        $solutions = [];

        if ($isValidBoard) {
            $board = QueensProblem::initBlockedFields($board);
            $solutions = QueensProblem::_recursivelyPlaceQueens($board);
        }

        $result = [
            'isValidBoard' => $isValidBoard,
            'solutions' => $solutions,
            'board' => $board,
            'boardSize' => sprintf("%dx%d", sizeof($board), sizeof($board)),
        ];

        return $result;
    }

    public static function print($board) {
        printf("Board: %dx%d\r\n", sizeof($board), sizeof($board[0]));
        foreach ($board as $rowIndex => $row) {
            foreach ($row as $columnIndex => $tile) {
                printf("|%02d|", $tile);
            }
            printf("\r\n");
        }
        printf("End Board\r\n");
    }

    public static function _recursivelyPlaceQueens($board, $startRow = 0) {
        $board = QueensProblem::cloneBoard($board);

        $solutions = [];
        $boardSize = sizeof($board);
        for ($rowIndex = $startRow; $rowIndex < $boardSize; $rowIndex++) {
            for ($columnIndex = 0; $columnIndex < $boardSize; $columnIndex++) {
                $tile = $board[$rowIndex][$columnIndex];
                if ($tile == QueensProblem::TILE_EMPTY_VALUE) {
                    $newBoard = QueensProblem::_placeQueenOnBoard($board, $rowIndex, $columnIndex);

                    if (!QueensProblem::hasQueenSpace($newBoard)) {
                        if (QueensProblem::countQueensOnBoard($newBoard) >= sizeof($newBoard)) {
                            $solutions[] = $newBoard;
                        } else {
                            $stuckBoards[] = $newBoard;
                        }
                    } else { /* recurse with the new board */
                        $solutions = array_merge($solutions, QueensProblem::_recursivelyPlaceQueens($newBoard, $rowIndex+1));
                    }
                }
            }
        }

        return $solutions;
    }

    /**
     * Creates and returns a copy of the board
     */
    public static function cloneBoard($board) {
        $newBoard = [];

        foreach ($board as $rowIndex => $row) {
            $newRow = [];
            foreach ($row as $columnIndex => $tile) {
                $newRow[] = $tile;
            }
            $newBoard[] = $newRow;
        }
        return $newBoard;
    }

    /**
     * Checks if there would be room for another queen on this board
     */
    public static function hasQueenSpace($board) {
        if (QueensProblem::countQueensOnBoard($board) >= sizeof($board)) {
            return false;
        }

        foreach ($board as $rowIndex => $row) {
            foreach ($row as $columnIndex => $tile) {
                if ($tile == QueensProblem::TILE_EMPTY_VALUE) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Reloads the blocked fields on a board with queens.
     * Assumes that there are no queens that could strike eachother.
     */
    public static function initBlockedFields($board) {
        foreach ($board as $rowIndex => $row) {
            foreach ($row as $columnIndex => $tile) {
                if ($tile == QueensProblem::TILE_QUEEN_VALUE) {
                    $board[$rowIndex][$columnIndex] = QueensProblem::TILE_EMPTY_VALUE;
                    $board = QueensProblem::_placeQueenOnBoard($board, $rowIndex, $columnIndex);
                }
            }
        }        
        return $board;
    }

    /**
     * Counts the number of queens on the board
     */
    public static function countQueensOnBoard($board) {
        $count = 0;
        foreach ($board as $rowIndex => $row) {
            foreach ($row as $columnIndex => $tile) {
                if ($tile == QueensProblem::TILE_QUEEN_VALUE) {
                    $count++;
                }
            }
        }
        return $count;
    }

    /**
     * Places a queen on the board at the given row and column
     * Also blocks the fields the new queen is theatening
     * @return $board 
     */
    public static function _placeQueenOnBoard($board, $rowIndex, $columnIndex) {
        if ($board[$rowIndex][$columnIndex] != QueensProblem::TILE_EMPTY_VALUE) {
            echo "error: ";
            QueensProblem::print($board);
            throw new Error(sprintf('cannot place queen on tile [%d, %d])', $rowIndex, $columnIndex));
        }

        for ($i = 0; $i < sizeof($board); $i++) {
            // block all tiles on row
            $board[$i][$columnIndex] = QueensProblem::TILE_BLOCKED_VALUE;
            // block all tiles on column
            $board[$rowIndex][$i] = QueensProblem::TILE_BLOCKED_VALUE;
            // blocks all tiles on positive diagonal to the right
            $diagonalRowIndex = $rowIndex + $i;
            $diagonalColumnIndex = $columnIndex + $i;
            if (QueensProblem::isValidBoardPosition($board, $diagonalRowIndex, $diagonalColumnIndex)) {
                $board[$diagonalRowIndex][$diagonalColumnIndex] = QueensProblem::TILE_BLOCKED_VALUE;
            }
            // blocks all tiles on positive diagonal to the left
            $diagonalRowIndex = $rowIndex + $i;
            $diagonalColumnIndex = $columnIndex - $i;
            if (QueensProblem::isValidBoardPosition($board, $diagonalRowIndex, $diagonalColumnIndex)) {
                $board[$diagonalRowIndex][$diagonalColumnIndex] = QueensProblem::TILE_BLOCKED_VALUE;
            }
            // blocks all tiles on negative diagonal to the right
            $diagonalRowIndex = $rowIndex - $i;
            $diagonalColumnIndex = $columnIndex + $i;
            if (QueensProblem::isValidBoardPosition($board, $diagonalRowIndex, $diagonalColumnIndex)) {
                $board[$diagonalRowIndex][$diagonalColumnIndex] = QueensProblem::TILE_BLOCKED_VALUE;
            }
            // blocks all tiles on negative diagonal to the left
            $diagonalRowIndex = $rowIndex - $i;
            $diagonalColumnIndex = $columnIndex - $i;
            if (QueensProblem::isValidBoardPosition($board, $diagonalRowIndex, $diagonalColumnIndex)) {
                $board[$diagonalRowIndex][$diagonalColumnIndex] = QueensProblem::TILE_BLOCKED_VALUE;
            }
        }
        $board[$rowIndex][$columnIndex] = QueensProblem::TILE_QUEEN_VALUE;
        return $board;
    }

    /**
     * Whether the board given as input is OK.
     *  - Has correct board dimensions (squared and > 1)
     *  - All rows have the same number of columns
     *  - Has no colliding queens on the board
     */
    public function isValidBoard($board) {
        if (!isset($board) || $board == null) {return false;}
        if (sizeof($board) <= 0) {return false;}
        if (sizeof($board) != sizeof($board[0])) {
            return false;
        }
        foreach ($board as $rowIndex => $row) {
            if (sizeof($row) != sizeof($board)) {
                return false;
            }
        }
        if (QueensProblem::hasCollidingQueens($board)) {
            return false;
        }
        return true;
    }

    /**
     * Whether there are colliding queens on the board
     */
    public function hasCollidingQueens($board) {
        foreach ($board as $rowIndex => $row) {
            foreach ($row as $columnIndex => $tile) {
                if ($tile == QueensProblem::TILE_QUEEN_VALUE) {
                    if (QueensProblem::tileHasCollidingQueen($board, $rowIndex, $columnIndex)) {
                        return true;
                    }
                }
            }
        }
    }

    /**
     * Tile has colliding queen if either:
     *  has colliding queen on the same row
     *  has colliding queen on the same column
     *  has colliding queen on the same diagonal
     */
    public function tileHasCollidingQueen($board, $tileRow, $tileColumn) {
        if (QueensProblem::countQueensOnRow($board, $tileRow) > 1) {
            return true;
        }
        if (QueensProblem::countQueensOnColumn($board, $tileColumn) > 1) {
            return true;
        }

        return false;
    }

    /**
     * Counts the number of queens on a given row
     */
    public function countQueensOnRow($board, $rowIndex) {
        $count = 0;
        foreach ($board[$rowIndex] as $tile) {
            if ($tile == QueensProblem::TILE_QUEEN_VALUE) {$count++;}
        }
        return $count;
    }

    /**
     * Counts the number of queens on a given column
     */
    public function countQueensOnColumn($board, $columnIndex) {
        $count = 0;
        foreach ($board as $row) {
            $tile = $row[$columnIndex];
            if ($tile == QueensProblem::TILE_QUEEN_VALUE) {$count++;}
        }
        return $count;
    }

    /**
     * Counts the number of queens on a diagonal
     */
    public function countQueensOnDiagonal($board, $rowIndex, $columnIndex) {
        $boardSize = sizeof($board);

        for ($i = $boardSize,
            $diagonalRowLow = $rowIndex, $diagonalRowHigh = $rowIndex,
            $diagonalColumnLow = $columnIndex, $diagonalColumnHigh = $columnIndex;
            $i <= $boardSize;
            $diagonalRowLow--, $diagonalRowHigh++,
            $diagonalColumnLow--, $diagonalColumnHigh++) {

            /* Diagonal left down */
            if (QueensProblem::isValidBoardPosition($diagonalRowLow, $diagonalColumnLow)) {
                $tile = $board[$diagonalRowLow][$diagonalColumnLow];
                if ($tile == QueensProblem::TILE_QUEEN_VALUE) {
                    $count++;
                }
            }

            /* Diagonal right down */
            if (QueensProblem::isValidBoardPosition($diagonalRowLow, $diagonalColumnHigh)) {
                $tile = $board[$diagonalRowLow][$diagonalColumnHigh];
                if ($tile == QueensProblem::TILE_QUEEN_VALUE) {
                    $count++;
                }
            }

            /* Diagonal left up */
            if (QueensProblem::isValidBoardPosition($diagonalRowHigh, $diagonalColumnLow)) {
                $tile = $board[$diagonalRowHigh][$diagonalColumnLow];
                if ($tile == QueensProblem::TILE_QUEEN_VALUE) {
                    $count++;
                }
            }

            /* Diagonal right up */
            if (QueensProblem::isValidBoardPosition($board, $diagonalRowHigh, $diagonalColumnHigh)) {
                $tile = $board[$diagonalRowHigh][$diagonalColumnHigh];
                if ($tile == QueensProblem::TILE_QUEEN_VALUE) {
                    $count++;
                }
            }
        }

        return $count;
    }

    /**
     * Whether the row, column combination exists on the board
     */
    public function isValidBoardPosition($board, $rowIndex, $columnIndex) {
        $boardSize = sizeof($board);
        if (isInRange($rowIndex, 0, $boardSize) &&
            isInRange($columnIndex, 0, $boardSize)) {
            return true;
        }
        return false;
    }
}

/**
 * Helper function that checks if $i is in the range($min, $max);
 */
function isInRange($i, $min, $max) {
    return $i >= $min && $i < $max;
}

/**
 * The PHP modulo operator % returns negative numbers. e.g. -3 % 7 == -3.
 * Other languages will wrap the negative to produce a positive number, e.g. -3 % 7 == 4.
 * To implement a true modulo operator, you have to write a custom function:
 *
 * Source: http://mindspill.net/computing/web-development-notes/php/php-modulo-operator-returns-negative-numbers/
 */
function mod($num, $m) {
    return ($num % $m + $m) % $m;
}
